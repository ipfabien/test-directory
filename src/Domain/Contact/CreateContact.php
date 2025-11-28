<?php

declare(strict_types=1);

namespace App\Domain\Contact;

use Webmozart\Assert\Assert;

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
        $this->lastname  = $lastname;
        $this->email     = $email;
        $this->phone     = $phone;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function create(
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone = null
    ): self {
        Assert::stringNotEmpty($firstname, 'Firstname should not be empty.');
        Assert::maxLength($firstname, 100, 'Firstname is too long.');

        Assert::stringNotEmpty($lastname, 'Lastname should not be empty.');
        Assert::maxLength($lastname, 100, 'Lastname is too long.');

        Assert::email($email, 'Email is invalid.');
        Assert::maxLength($email, 255, 'Email is too long.');

        Assert::nullOrString($phone);

        if ($phone !== null) {
            Assert::maxLength($phone, 32, 'Phone is too long.');
        }

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
