<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Shared\Normalization\Normalizable;

/**
 * DTO representing the payload to create a contact.
 */
final class CreateContactRequest implements Normalizable
{
    private string $firstname;

    private string $lastname;

    private string $email;

    private ?string $phone;

    private ?string $note;

    public function __construct(
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone = null,
        ?string $note = null
    ) {
        $this->firstname = $firstname;
        $this->lastname  = $lastname;
        $this->email     = $email;
        $this->phone     = $phone;
        $this->note      = $note;
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

    /**
     * @param array<mixed> $data
     */
    public static function denormalize(array $data): self
    {
        return new self(
            $data['firstname'] ?? '',
            $data['lastname']  ?? '',
            $data['email']     ?? '',
            $data['phone']     ?? null,
            $data['note']      ?? null
        );
    }

    /**
     * @return array<mixed>
     */
    public function normalize(): array
    {
        return [
            'firstname' => $this->firstname,
            'lastname'  => $this->lastname,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'note'      => $this->note,
        ];
    }
}
