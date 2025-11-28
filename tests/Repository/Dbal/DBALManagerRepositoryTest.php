<?php

declare(strict_types=1);

namespace App\Tests\Repository\Dbal;

use App\Domain\Manager\Manager;
use App\Domain\Manager\ManagerList;
use App\Domain\Shared\ExternalId;
use App\Services\Repository\Dbal\DBALManagerRepository;
use App\Shared\Exception\NotFoundException;
use App\Tests\Database\TransactionalDbalTestCase;

final class DBALManagerRepositoryTest extends TransactionalDbalTestCase
{
    private DBALManagerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new DBALManagerRepository($this->connection);
    }

    public function testFindReturnsManagerForExistingExternalId(): void
    {
        // externalId seeded in migration Version20251128010000
        $externalId = ExternalId::fromString('11111111-1111-1111-1111-111111111111');

        $manager = $this->repository->find($externalId);

        self::assertInstanceOf(Manager::class, $manager);
        self::assertTrue($manager->externalId()->equals($externalId));
    }

    public function testFindThrowsNotFoundExceptionForUnknownExternalId(): void
    {
        $this->expectException(NotFoundException::class);

        $externalId = ExternalId::fromString('99999999-9999-9999-9999-999999999999');
        $this->repository->find($externalId);
    }

    public function testFindAllReturnsManagerList(): void
    {
        $list = $this->repository->findAll();

        self::assertInstanceOf(ManagerList::class, $list);
        self::assertGreaterThanOrEqual(1, \count($list));
    }
}


