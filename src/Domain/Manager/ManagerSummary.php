<?php

declare(strict_types=1);

namespace App\Domain\Manager;

use App\Domain\Shared\ExternalId;
use App\Shared\Normalization\Normalizable;

/**
 * Value object representing a lightweight view of a manager.
 */
final class ManagerSummary implements Normalizable
{
    private ExternalId $externalId;

    private string $firstname;

    private string $lastname;

    public function __construct(
        ExternalId $externalId,
        string $firstname,
        string $lastname
    ) {
        $this->externalId = $externalId;
        $this->firstname  = $firstname;
        $this->lastname   = $lastname;
    }

    public static function create(
        ExternalId $externalId,
        string $firstname,
        string $lastname
    ): self {
        return new self($externalId, $firstname, $lastname);
    }

    public function externalId(): ExternalId
    {
        return $this->externalId;
    }

    public function firstname(): string
    {
        return $this->firstname;
    }

    public function lastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param array<mixed> $data
     */
    public static function denormalize(array $data): self
    {
        return new self(
            ExternalId::fromString((string) ($data['externalId'] ?? '')),
            (string) ($data['firstname'] ?? ''),
            (string) ($data['lastname'] ?? '')
        );
    }

    /**
     * @return array<mixed>
     */
    public function normalize(): array
    {
        return [
            'externalId' => $this->externalId->toString(),
            'firstname'  => $this->firstname,
            'lastname'   => $this->lastname,
        ];
    }
}
