<?php

declare(strict_types=1);

use Doctrine\DBAL\DriverManager;

require __DIR__ . '/../vendor/autoload.php';

// Ensure test database exists (separate from dev DB).
$dbName = getenv('DB_NAME') ?: 'contacts_test';

$connectionParams = [
    'driver'   => 'pdo_pgsql',
    'host'     => (string) (getenv('DB_HOST') ?: 'db'),
    'port'     => (int) (getenv('DB_PORT') ?: 5432),
    'dbname'   => 'postgres',
    'user'     => (string) (getenv('DB_USER') ?: 'contacts'),
    'password' => (string) (getenv('DB_PASSWORD') ?: 'contacts'),
];

try {
    $tmpConnection = DriverManager::getConnection($connectionParams);

    // Create database if it does not exist yet.
    $safeName = str_replace('"', '""', $dbName);
    $tmpConnection->executeStatement(sprintf('CREATE DATABASE "%s"', $safeName));
} catch (\Throwable $exception) {
    // If database already exists or cannot be created, we just proceed.
    // Tests will fail later if the database is really unusable.
} finally {
    if (isset($tmpConnection)) {
        $tmpConnection->close();
    }
}

// Run Doctrine migrations once before the test suite to ensure schema is up to date in the test DB.
$command = 'php bin/console doctrine:migrations:migrate --no-interaction';
exec($command, $output, $exitCode);

if ($exitCode !== 0) {
    fwrite(STDERR, "Doctrine migrations failed with exit code {$exitCode}\n");
    exit($exitCode);
}


