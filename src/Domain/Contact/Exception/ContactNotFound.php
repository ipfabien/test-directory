<?php

declare(strict_types=1);

namespace App\Domain\Contact\Exception;

final class ContactNotFound extends \RuntimeException
{
    public static function forExternalId(string $externalId): self
    {
        return new self(sprintf('Contact with externalId "%s" not found.', $externalId));
    }
}


