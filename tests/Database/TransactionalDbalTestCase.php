<?php

declare(strict_types=1);

namespace App\Tests\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

/**
 * Base test case for DBAL-based integration tests.
 *
 * It wraps each test in its own database transaction to avoid polluting the test database.
 */
abstract class TransactionalDbalTestCase extends TestCase
{
    protected Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = DriverManager::getConnection([
            'driver'   => 'pdo_pgsql',
            'host'     => (string) (getenv('DB_HOST') ?: 'db'),
            'port'     => (int) (getenv('DB_PORT') ?: 5432),
            'dbname'   => (string) (getenv('DB_NAME') ?: 'contacts_test'),
            'user'     => (string) (getenv('DB_USER') ?: 'contacts'),
            'password' => (string) (getenv('DB_PASSWORD') ?: 'contacts'),
        ]);

        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        if (isset($this->connection) && $this->connection->isTransactionActive()) {
            $this->connection->rollBack();
        }

        if (isset($this->connection)) {
            $this->connection->close();
        }

        parent::tearDown();
    }
}


