<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191202081004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE cards_changes (id UUID NOT NULL, card_number VARCHAR(255) DEFAULT NULL, day_limit BIGINT NOT NULL, week_limit BIGINT NOT NULL, month_limit BIGINT NOT NULL, service_schedule VARCHAR(255) NOT NULL, time_use_from TIME(0) WITHOUT TIME ZONE NOT NULL, time_use_to TIME(0) WITHOUT TIME ZONE NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_899CE674E4AF4C20 ON cards_changes (card_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_899CE674BF396750 ON cards_changes (id)');
        $this->addSql('COMMENT ON COLUMN cards_changes.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cards_changes.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cards_changes.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'CREATE TABLE fuel_limits_change (id UUID NOT NULL, card_number VARCHAR(255) NOT NULL, fuel_code VARCHAR(255) NOT NULL, day_limit BIGINT NOT NULL, week_limit BIGINT NOT NULL, month_limit BIGINT NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17625954BF396750 ON fuel_limits_change (id)');
        $this->addSql('COMMENT ON COLUMN fuel_limits_change.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN fuel_limits_change.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN fuel_limits_change.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'ALTER TABLE cards_changes ADD CONSTRAINT FK_899CE674E4AF4C20 FOREIGN KEY (card_number) REFERENCES cards (card_number) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('DROP INDEX uniq_3390d69a676a6889');
        $this->addSql('ALTER TABLE fuel_types DROP constraint fuel_types_pkey;');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3390D69ABF396750 ON fuel_types (id)');
        $this->addSql('ALTER TABLE fuel_types ADD PRIMARY KEY (fuel_code)');
        $this->addSql('DROP INDEX uniq_4c258fde4af4c20 CASCADE;');
        $this->addSql('ALTER TABLE cards DROP constraint cards_pkey;');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C258FDBF396750 ON cards (id)');
        $this->addSql('ALTER TABLE cards ADD PRIMARY KEY (card_number)');
        $this->addSql('ALTER TABLE fuel_limits ADD status SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE fuel_limits DROP purse_activity');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE cards_changes');
        $this->addSql('DROP TABLE fuel_limits_change');
        $this->addSql('DROP INDEX UNIQ_3390D69ABF396750');
        $this->addSql('DROP INDEX fuel_types_pkey');
        $this->addSql('CREATE UNIQUE INDEX uniq_3390d69a676a6889 ON fuel_types (fuel_code)');
        $this->addSql('ALTER TABLE fuel_types ADD PRIMARY KEY (id)');
        $this->addSql('DROP INDEX UNIQ_4C258FDBF396750');
        $this->addSql('DROP INDEX cards_pkey');
        $this->addSql('CREATE UNIQUE INDEX uniq_4c258fde4af4c20 ON cards (card_number)');
        $this->addSql('ALTER TABLE cards ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE fuel_limits ADD purse_activity BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE fuel_limits DROP status');
    }
}
