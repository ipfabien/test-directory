<?php

declare(strict_types=1);

namespace App\Domain\Contact;

/**
 * Value object representing the intent to create a contact.
 */
final class CreateContact
{
    private string $firstname;

    private string $lastname;

    private string $email;

    private ?string $phone;

    private function __construct(
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone = null
    ) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->phone = $phone;
    }

    public static function create(
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone = null
    ): self {
        return new self($firstname, $lastname, $email, $phone);
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
}


