<?php

declare(strict_types=1);

namespace App\Domain\Manager;

use App\Domain\Shared\ExternalId;
use App\Shared\Exception\NotFoundException;
use App\Shared\Exception\RuntimeException;

interface ManagerRepository
{
    /**
     * @throws NotFoundException
     * @throws RuntimeException
     */
    public function find(ExternalId $externalId): Manager;

    /**
     * @throws RuntimeException
     */
    public function findAll(): ManagerList;
}
