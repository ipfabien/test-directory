<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use Webmozart\Assert\Assert;

final class ExternalId
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        Assert::uuid($value, 'External id must be a valid UUID.');

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(ExternalId $other): bool
    {
        return $this->value === $other->value;
    }
}


