<?php

declare(strict_types=1);

namespace App\Domain\Contact;

use App\Domain\Shared\Pagination;
use App\Domain\Shared\ExternalId;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\NotFoundException;
use App\Shared\Exception\RuntimeException;

interface ContactRepository
{
    /**
     * @throws BadRequestException
     * @throws RuntimeException
     */
    public function create(CreateContact $contact): ExternalId;

    /**
     * @throws NotFoundException
     * @throws RuntimeException
     */
    public function find(ExternalId $externalId): Contact;

    /**
     * @throws RuntimeException
     */
    public function search(SearchFilter $filter, Pagination $pagination): ContactList;
}


