<?php

declare(strict_types=1);

namespace App\Services\Repository\Dbal;

use App\Domain\Manager\Manager;
use App\Domain\Manager\ManagerList;
use App\Domain\Manager\ManagerRepository;
use App\Domain\Manager\ManagerSummary;
use App\Domain\Shared\ExternalId;
use App\Shared\Exception\NotFoundException;
use App\Shared\Exception\RuntimeException;
use Doctrine\DBAL\Connection;

final class DBALManagerRepository implements ManagerRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function find(ExternalId $externalId): Manager
    {
        try {
            $row = $this->connection->fetchAssociative(
                'SELECT external_id, firstname, lastname FROM manager WHERE external_id = :external_id',
                ['external_id' => $externalId->toString()]
            );
        } catch (\Throwable $exception) {
            throw new RuntimeException('Unable to load manager.', $exception);
        }

        if ($row === false) {
            throw new NotFoundException(sprintf('Manager with externalId "%s" not found.', $externalId->toString()));
        }

        return Manager::create(
            ExternalId::fromString((string) $row['external_id']),
            (string) $row['firstname'],
            (string) $row['lastname']
        );
    }

    /**
     * @inheritDoc
     */
    public function findAll(): ManagerList
    {
        try {
            $rows = $this->connection->fetchAllAssociative(
                'SELECT external_id, firstname, lastname FROM manager ORDER BY lastname, firstname'
            );
        } catch (\Throwable $exception) {
            throw new RuntimeException('Unable to load managers.', $exception);
        }

        $managers = [];

        foreach ($rows as $row) {
            $managers[] = ManagerSummary::create(
                ExternalId::fromString((string) $row['external_id']),
                (string) $row['firstname'],
                (string) $row['lastname']
            );
        }

        return new ManagerList(...$managers);
    }
}
