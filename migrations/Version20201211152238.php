<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201211152238 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE partners DROP COLUMN IF EXISTS credit_limit');

        $this->addSql('ALTER TABLE partners ADD COLUMN credit_limit BIGINT NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE partners ALTER COLUMN credit_limit DROP DEFAULT ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE partners DROP COLUMN credit_limit');
    }
}
