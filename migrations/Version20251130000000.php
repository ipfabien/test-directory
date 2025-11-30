<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251130000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create messenger_messages table for Symfony Messenger Doctrine transport.';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('messenger_messages')) {
            return;
        }

        $this->addSql(
            'CREATE TABLE messenger_messages (
                id BIGSERIAL PRIMARY KEY,
                body TEXT NOT NULL,
                headers TEXT NOT NULL,
                queue_name VARCHAR(190) NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
            )'
        );

        $this->addSql('CREATE INDEX IDX_MESSENGER_MESSAGES_QUEUE_NAME ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_MESSENGER_MESSAGES_AVAILABLE_AT ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_MESSENGER_MESSAGES_DELIVERED_AT ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable('messenger_messages')) {
            return;
        }

        $this->addSql('DROP INDEX IF EXISTS IDX_MESSENGER_MESSAGES_QUEUE_NAME');
        $this->addSql('DROP INDEX IF EXISTS IDX_MESSENGER_MESSAGES_AVAILABLE_AT');
        $this->addSql('DROP INDEX IF EXISTS IDX_MESSENGER_MESSAGES_DELIVERED_AT');
        $this->addSql('DROP TABLE messenger_messages');
    }
}


