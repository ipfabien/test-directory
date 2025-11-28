<?php

declare(strict_types=1);

namespace App\Domain\Contact;

use App\Domain\Manager\Manager;
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

    private ?string $note;

    private ?Manager $manager;

    private function __construct(
        ExternalId $externalId,
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone,
        ?string $note = null,
        ?Manager $manager = null
    ) {
        $this->externalId = $externalId;
        $this->firstname  = $firstname;
        $this->lastname   = $lastname;
        $this->email      = $email;
        $this->phone      = $phone;
        $this->note       = $note;
        $this->manager    = $manager;
    }

    public static function create(
        ExternalId $externalId,
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone,
        ?string $note = null,
        ?Manager $manager = null
    ): self {
        return new self($externalId, $firstname, $lastname, $email, $phone, $note, $manager);
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

    public function note(): ?string
    {
        return $this->note;
    }

    public function manager(): ?Manager
    {
        return $this->manager;
    }

    /**
     * @param array<mixed> $data
     */
    public static function denormalize(array $data): self
    {
        $manager = null;

        if (isset($data['manager']) && \is_array($data['manager'])) {
            $manager = Manager::denormalize($data['manager']);
        }

        return new self(
            ExternalId::fromString((string) ($data['externalId'] ?? '')),
            (string) ($data['firstname'] ?? ''),
            (string) ($data['lastname'] ?? ''),
            (string) ($data['email'] ?? ''),
            isset($data['phone']) ? (string) $data['phone'] : null,
            isset($data['note']) ? (string) $data['note'] : null,
            $manager
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
            'email'      => $this->email,
            'phone'      => $this->phone,
            'note'       => $this->note,
            'manager'    => $this->manager !== null ? $this->manager->normalize() : null,
        ];
    }
}
