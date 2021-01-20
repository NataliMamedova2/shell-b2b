<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120152010 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE fuel_types (id UUID NOT NULL, fuel_code VARCHAR(255) NOT NULL, fuel_name VARCHAR(255) NOT NULL, fuel_purse BOOLEAN NOT NULL, fuel_type INT DEFAULT NULL, additional_type INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3390D69A676A6889 ON fuel_types (fuel_code)');
        $this->addSql('CREATE INDEX IDX_3390D69A676A6889 ON fuel_types (fuel_code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3390D69ABF396750676A6889 ON fuel_types (id, fuel_code)');
        $this->addSql('COMMENT ON COLUMN fuel_types.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN fuel_types.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN fuel_types.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE fuel_types');
    }
}
