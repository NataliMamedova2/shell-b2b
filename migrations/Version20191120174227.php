<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120174227 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE cards (id UUID NOT NULL, client_1c_id VARCHAR(255) NOT NULL, card_number VARCHAR(255) NOT NULL, car_number VARCHAR(255) DEFAULT NULL, day_limit VARCHAR(255) NOT NULL, week_limit VARCHAR(255) NOT NULL, month_limit VARCHAR(255) NOT NULL, service_schedule VARCHAR(255) NOT NULL, time_use_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, time_use_to TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, card_status BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C258FDE4AF4C20 ON cards (card_number)');
        $this->addSql('CREATE INDEX IDX_4C258FDE4AF4C20 ON cards (card_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C258FDBF396750E4AF4C20 ON cards (id, card_number)');
        $this->addSql('COMMENT ON COLUMN cards.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cards.time_use_from IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cards.time_use_to IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cards.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cards.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE cards');
    }
}
