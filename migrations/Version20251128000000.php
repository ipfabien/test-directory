<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251128000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add nullable note column to contact table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact ADD COLUMN note TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact DROP COLUMN note');
    }
}


