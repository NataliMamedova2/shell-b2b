<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201117202236 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE replacement_fuel_types (id UUID NOT NULL, fuel_code VARCHAR(255) NOT NULL, fuel_replacement_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F267B220676A6889 ON replacement_fuel_types (fuel_code)');
        $this->addSql('CREATE INDEX IDX_F267B220676A6889 ON replacement_fuel_types (fuel_code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F267B220BF396750676A6889 ON replacement_fuel_types (id, fuel_code)');
        $this->addSql('COMMENT ON COLUMN replacement_fuel_types.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE clients_info DROP client_pc_id_old');
        $this->addSql('ALTER TABLE clients_info DROP fc_cbr_id_old');
        $this->addSql('ALTER TABLE clients_info DROP balance_old');
        $this->addSql('ALTER TABLE clients_info DROP credit_limit_old');
        $this->addSql('ALTER TABLE clients ALTER edrpou_inn SET DEFAULT NULL');
        $this->addSql('ALTER TABLE clients ALTER edrpou_inn TYPE VARCHAR(12)');
        $this->addSql('ALTER TABLE clients ALTER sota_token SET DEFAULT NULL');
        $this->addSql('ALTER TABLE clients ALTER sota_token TYPE VARCHAR(33)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE replacement_fuel_types');
        $this->addSql('ALTER TABLE clients_info ADD client_pc_id_old VARCHAR(14) DEFAULT NULL');
        $this->addSql('ALTER TABLE clients_info ADD fc_cbr_id_old VARCHAR(14) DEFAULT NULL');
        $this->addSql('ALTER TABLE clients_info ADD balance_old DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE clients_info ADD credit_limit_old BIGINT DEFAULT NULL');
    }
}
