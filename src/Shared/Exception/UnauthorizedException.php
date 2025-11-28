<?php

declare(strict_types=1);

namespace App\Shared\Exception;

final class UnauthorizedException extends AppException
{
    public function __construct(string $message = 'Unauthorized', ?\Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}
