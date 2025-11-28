<?php

declare(strict_types=1);

namespace App\Services\Http\ArgumentResolver;

use App\Shared\Exception\BadRequestException;
use App\Shared\Normalization\Normalizable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Resolves controller arguments implementing Normalizable from a JSON request body.
 */
final class JsonBodyNormalizableResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        // Only handle JSON body for write operations (POST/PUT/PATCH)
        if (!\in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            return false;
        }

        $type = $argument->getType();

        if ($type === null || !class_exists($type)) {
            return false;
        }

        if (!is_subclass_of($type, Normalizable::class)) {
            return false;
        }

        $contentType = (string) $request->headers->get('Content-Type', '');

        if (strpos($contentType, 'application/json') !== 0) {
            return false;
        }

        return true;
    }

    /**
     * @throws BadRequestException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $content = $request->getContent();

        if ($content === '' || $content === null) {
            $data = [];
        } else {
            try {
                $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                throw new BadRequestException('Invalid JSON body.', $exception);
            }

            if (!\is_array($data)) {
                throw new BadRequestException('Invalid JSON body.');
            }
        }

        /** @var class-string<Normalizable> $class */
        $class = (string) $argument->getType();

        yield $class::denormalize($data);
    }
}


