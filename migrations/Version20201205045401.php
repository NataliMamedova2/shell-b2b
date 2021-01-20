<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201205045401 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE partners DROP COLUMN IF EXISTS contract_number');
        $this->addSql('ALTER TABLE partners ADD COLUMN contract_number VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE partners ADD COLUMN contract_date   date');
        $this->addSql('ALTER TABLE partners ADD COLUMN balance BIGINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE partners DROP COLUMN IF EXISTS contract_number');
        $this->addSql('ALTER TABLE partners DROP COLUMN IF EXISTS contract_date');
        $this->addSql('ALTER TABLE partners DROP COLUMN IF EXISTS balance');
    }
}
