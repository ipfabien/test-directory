<?php

declare(strict_types=1);

namespace App\Message\Event\Contact;

/**
 * Event dispatched when a contact has been created.
 *
 * It is purposely simple and contains only the data needed
 * by asynchronous handlers (like sending an email).
 */
final class ContactCreated
{
    private string $externalId;

    private string $firstname;

    private string $lastname;

    private string $email;

    private function __construct(string $externalId, string $firstname, string $lastname, string $email)
    {
        $this->externalId = $externalId;
        $this->firstname  = $firstname;
        $this->lastname   = $lastname;
        $this->email      = $email;
    }

    public static function create(string $externalId, string $firstname, string $lastname, string $email): self
    {
        return new self($externalId, $firstname, $lastname, $email);
    }

    public function externalId(): string
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
}
