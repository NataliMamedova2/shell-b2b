<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191206132009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create cards_stop_list table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE cards_stop_list (id UUID NOT NULL, card_number VARCHAR(255) DEFAULT NULL, sync_status SMALLINT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_3461B04E4AF4C20 ON cards_stop_list (card_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3461B04E4AF4C20D3818BC3 ON cards_stop_list (card_number, sync_status)');
        $this->addSql('COMMENT ON COLUMN cards_stop_list.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cards_stop_list.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cards_stop_list.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'ALTER TABLE cards_stop_list ADD CONSTRAINT FK_3461B04E4AF4C20 FOREIGN KEY (card_number) REFERENCES cards (card_number) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('DROP TABLE stop_list');
        $this->addSql('DROP INDEX uniq_899ce674bf396750');
        $this->addSql(
            'ALTER TABLE cards_changes ADD CONSTRAINT FK_899CE674E4AF4C20 FOREIGN KEY (card_number) REFERENCES cards (card_number) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_899CE674E4AF4C207B00651C ON cards_changes (card_number, status)');
        $this->addSql('DROP INDEX uniq_17625954bf396750');
        $this->addSql('ALTER TABLE fuel_limits_change ADD purse_activity SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17625954E4AF4C20676A6889 ON fuel_limits_change (card_number, fuel_code)');
        $this->addSql('DROP INDEX uniq_60aad212bf396750');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_60AAD212E4AF4C20676A6889 ON fuel_limits (card_number, fuel_code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE stop_list (id UUID NOT NULL, card_number VARCHAR(255) NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX idx_ae2e3a07e4af4c20 ON stop_list (card_number)');
        $this->addSql('CREATE UNIQUE INDEX uniq_ae2e3a07e4af4c207b00651c ON stop_list (card_number, status)');
        $this->addSql('COMMENT ON COLUMN stop_list.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN stop_list.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN stop_list.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP TABLE cards_stop_list');
        $this->addSql('DROP INDEX UNIQ_17625954E4AF4C20676A6889');
        $this->addSql('ALTER TABLE fuel_limits_change DROP purse_activity');
        $this->addSql('CREATE UNIQUE INDEX uniq_17625954bf396750 ON fuel_limits_change (id)');
        $this->addSql('ALTER TABLE cards_changes DROP CONSTRAINT FK_899CE674E4AF4C20');
        $this->addSql('DROP INDEX UNIQ_899CE674E4AF4C207B00651C');
        $this->addSql('CREATE UNIQUE INDEX uniq_899ce674bf396750 ON cards_changes (id)');
        $this->addSql('DROP INDEX UNIQ_60AAD212E4AF4C20676A6889');
        $this->addSql('CREATE UNIQUE INDEX uniq_60aad212bf396750 ON fuel_limits (id)');
    }
}
