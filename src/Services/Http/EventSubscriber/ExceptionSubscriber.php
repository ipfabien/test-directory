<?php

declare(strict_types=1);

namespace App\Services\Http\EventSubscriber;

use App\Shared\Exception\AppException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, array<int, array{0: string}>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [['onKernelException']],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        // App-level exceptions with embedded HTTP codes
        if ($throwable instanceof AppException) {
            $response = $this->createJsonErrorResponse($throwable->getMessage(), $throwable->getCode() ?: 500);
        }

        // Validation errors from VO / value objects (webmozart/assert)
        if ($throwable instanceof \InvalidArgumentException) {
            $response = $this->createJsonErrorResponse($throwable->getMessage(), 400);
        }

        // Fallback: generic 500 for any other unexpected error
        $event->setResponse(
            $response ?? $this->createJsonErrorResponse('Internal Server Error', 500)
        );
    }

    private function createJsonErrorResponse(string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse(['error' => $message], $statusCode);
    }
}


