<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200217091819 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE clients_balance_history (id UUID NOT NULL, client_pc_id BIGINT DEFAULT NULL, balance BIGINT NOT NULL, date DATE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F1F86A4D2C0ECFAA ON clients_balance_history (client_pc_id)');
        $this->addSql('COMMENT ON COLUMN clients_balance_history.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients_balance_history.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN clients_balance_history.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE clients_info DROP constraint clients_info_pkey;');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_45AF18B6BF396750 ON clients_info (id)');
        $this->addSql('ALTER TABLE clients_info ADD PRIMARY KEY (client_pc_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE clients_balance_history');
        $this->addSql('DROP INDEX UNIQ_45AF18B6BF396750');
        $this->addSql('ALTER TABLE clients_info DROP CONSTRAINT clients_info_pkey');
        $this->addSql('ALTER TABLE clients_info ADD PRIMARY KEY (id)');
    }
}
