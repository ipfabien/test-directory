<?php

declare(strict_types=1);

namespace App\Domain\Contact;

use App\Domain\Shared\Pagination;

interface ContactRepository
{
    public function create(CreateContact $contact): string;

    public function find(string $externalId): Contact;

    public function search(SearchFilter $filter, Pagination $pagination): ContactList;
}


