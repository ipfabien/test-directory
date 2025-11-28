<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Shared\Normalization\Normalizable;

/**
 * DTO representing the request to get a single contact.
 */
final class GetContactRequest implements Normalizable
{
    private string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function denormalize(array $data): self
    {
        return new self((string) ($data['id'] ?? ''));
    }

    public function normalize(): array
    {
        return [
            'id' => $this->id,
        ];
    }

    public function id(): string
    {
        return $this->id;
    }
}
