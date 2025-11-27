<?php

declare(strict_types=1);

namespace App\Domain\Contact;

interface ContactRepository
{
    public function create(CreateContact $contact): string;

    public function find(string $externalId): Contact;

    public function search(): ContactList;
}


