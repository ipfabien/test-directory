<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Shared\Normalization\Normalizable;

/**
 * DTO representing filters and pagination for the contact list.
 */
final class GetContactListRequest implements Normalizable
{
    private ?string $firstname;

    private ?string $lastname;

    private ?string $email;

    private ?string $phone;

    private ?int $page;

    private ?int $perPage;

    private function __construct(
        ?string $firstname,
        ?string $lastname,
        ?string $email,
        ?string $phone,
        ?int $page,
        ?int $perPage
    ) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->phone = $phone;
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public static function denormalize(array $data): self
    {
        return new self(
            isset($data['firstname']) ? (string) $data['firstname'] : null,
            isset($data['lastname']) ? (string) $data['lastname'] : null,
            isset($data['email']) ? (string) $data['email'] : null,
            isset($data['phone']) ? (string) $data['phone'] : null,
            isset($data['page']) ? (int) $data['page'] : null,
            isset($data['perPage']) ? (int) $data['perPage'] : null
        );
    }

    public function normalize(): array
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'page' => $this->page,
            'perPage' => $this->perPage,
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

    public function page(): ?int
    {
        return $this->page;
    }

    public function perPage(): ?int
    {
        return $this->perPage;
    }
}


