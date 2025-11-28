<?php

declare(strict_types=1);

namespace App\Services\Http\ArgumentResolver;

use App\Shared\Normalization\Normalizable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Resolves controller arguments implementing Normalizable from route attributes and query parameters.
 */
final class QueryNormalizableResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        // Handle read operations (e.g. GET, DELETE)
        if (!\in_array($request->getMethod(), ['GET', 'DELETE'], true)) {
            return false;
        }

        $type = $argument->getType();

        if ($type === null || !class_exists($type)) {
            return false;
        }

        if (!is_subclass_of($type, Normalizable::class)) {
            return false;
        }

        return true;
    }

    /**
     * @return iterable<Normalizable>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = array_merge(
            $request->attributes->all(),
            $request->query->all()
        );

        /** @var class-string<Normalizable> $class */
        $class = (string) $argument->getType();

        yield $class::denormalize($data);
    }
}
