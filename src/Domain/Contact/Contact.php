<?php

declare(strict_types=1);

namespace App\Domain\Contact;

use App\Domain\Shared\ExternalId;
use App\Shared\Normalization\Normalizable;

/**
 * Value object representing a contact in the domain.
 */
final class Contact implements Normalizable
{
    private ExternalId $externalId;

    private string $firstname;

    private string $lastname;

    private string $email;

    private ?string $phone;

    private function __construct(
        ExternalId $externalId,
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone
    ) {
        $this->externalId = $externalId;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->phone = $phone;
    }

    public static function create(
        ExternalId $externalId,
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone
    ): self {
        return new self($externalId, $firstname, $lastname, $email, $phone);
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

    public function email(): string
    {
        return $this->email;
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param array<mixed> $data
     */
    public static function denormalize(array $data): self
    {
        return new self(
            ExternalId::fromString((string) ($data['externalId'] ?? '')),
            (string) ($data['firstname'] ?? ''),
            (string) ($data['lastname'] ?? ''),
            (string) ($data['email'] ?? ''),
            isset($data['phone']) ? (string) $data['phone'] : null
        );
    }

    /**
     * @return array<mixed>
     */
    public function normalize(): array
    {
        return [
            'externalId' => $this->externalId->toString(),
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}


