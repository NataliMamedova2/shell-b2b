<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120162528 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE fuel_prices (id UUID NOT NULL, fuel_code VARCHAR(255) NOT NULL, fuel_price INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D2A287F0676A6889 ON fuel_prices (fuel_code)');
        $this->addSql('CREATE INDEX IDX_D2A287F0676A6889 ON fuel_prices (fuel_code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D2A287F0BF396750676A6889 ON fuel_prices (id, fuel_code)');
        $this->addSql('COMMENT ON COLUMN fuel_prices.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN fuel_prices.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN fuel_prices.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE fuel_prices');
    }
}
