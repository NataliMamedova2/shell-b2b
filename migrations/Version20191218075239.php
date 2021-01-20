<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191218075239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add export_status';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cards ADD export_status SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE fuel_limits ADD export_status SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE cards_stop_list ADD client_1c_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE cards_stop_list DROP updated_at');
        $this->addSql('ALTER TABLE cards_stop_list RENAME COLUMN sync_status TO export_status');
        $this->addSql('DROP TABLE cards_changes');
        $this->addSql('DROP TABLE fuel_limits_change');
        $this->addSql('DROP INDEX uniq_3461b04e4af4c20d3818bc3');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE cards_changes (id UUID NOT NULL, card_number VARCHAR(255) DEFAULT NULL, day_limit BIGINT NOT NULL, week_limit BIGINT NOT NULL, month_limit BIGINT NOT NULL, service_schedule VARCHAR(255) NOT NULL, time_use_from TIME(0) WITHOUT TIME ZONE NOT NULL, time_use_to TIME(0) WITHOUT TIME ZONE NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX uniq_899ce674e4af4c207b00651c ON cards_changes (card_number, status)');
        $this->addSql('CREATE INDEX idx_899ce674e4af4c20 ON cards_changes (card_number)');
        $this->addSql('COMMENT ON COLUMN cards_changes.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cards_changes.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cards_changes.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'CREATE TABLE fuel_limits_change (id UUID NOT NULL, card_number VARCHAR(255) NOT NULL, fuel_code VARCHAR(255) NOT NULL, day_limit BIGINT NOT NULL, week_limit BIGINT NOT NULL, month_limit BIGINT NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, purse_activity SMALLINT DEFAULT 0 NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX uniq_17625954e4af4c20676a6889 ON fuel_limits_change (card_number, fuel_code)');
        $this->addSql('COMMENT ON COLUMN fuel_limits_change.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN fuel_limits_change.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN fuel_limits_change.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'ALTER TABLE cards_changes ADD CONSTRAINT fk_899ce674e4af4c20 FOREIGN KEY (card_number) REFERENCES cards (card_number) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE cards DROP export_status');
        $this->addSql('ALTER TABLE fuel_limits DROP export_status');
        $this->addSql('ALTER TABLE cards_stop_list ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE cards_stop_list DROP client_1c_id');
        $this->addSql('ALTER TABLE cards_stop_list RENAME COLUMN export_status TO sync_status');
        $this->addSql('COMMENT ON COLUMN cards_stop_list.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX uniq_3461b04e4af4c20d3818bc3 ON cards_stop_list (card_number, sync_status)');
    }
}
