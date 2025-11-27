<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Shared\Normalization\Normalizable;

/**
 * DTO representing filters for the contact list.
 */
final class GetContactListRequest implements Normalizable
{
    private ?string $firstname;

    private ?string $lastname;

    private ?string $email;

    private ?string $phone;

    private function __construct(
        ?string $firstname,
        ?string $lastname,
        ?string $email,
        ?string $phone
    ) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->phone = $phone;
    }

    public static function denormalize(array $data): self
    {
        return new self(
            isset($data['firstname']) ? (string) $data['firstname'] : null,
            isset($data['lastname']) ? (string) $data['lastname'] : null,
            isset($data['email']) ? (string) $data['email'] : null,
            isset($data['phone']) ? (string) $data['phone'] : null
        );
    }

    public function normalize(): array
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    public function firstname(): ?string
    {
        return $this->firstname;
    }

    public function lastname(): ?string
    {
        return $this->lastname;
    }

    public function email(): ?string
    {
        return $this->email;
    }

    public function phone(): ?string
    {
        return $this->phone;
    }
}


