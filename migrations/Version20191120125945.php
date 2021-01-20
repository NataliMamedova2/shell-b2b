<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120125945 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE fuel_cards (id UUID NOT NULL, client_1c_id VARCHAR(255) NOT NULL, card_number VARCHAR(255) NOT NULL, fuel_code VARCHAR(255) NOT NULL, day_limit VARCHAR(255) NOT NULL, week_limit VARCHAR(255) NOT NULL, month_limit VARCHAR(255) NOT NULL, purse_activity BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6E620757E4AF4C20 ON fuel_cards (card_number)');
        $this->addSql('CREATE INDEX IDX_6E620757E4AF4C20 ON fuel_cards (card_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6E620757BF396750E4AF4C20 ON fuel_cards (id, card_number)');
        $this->addSql('COMMENT ON COLUMN fuel_cards.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN fuel_cards.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN fuel_cards.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE fuel_cards');
    }
}
