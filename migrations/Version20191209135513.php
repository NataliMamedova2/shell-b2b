<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191209135513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create clients_info table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE clients_info (id UUID NOT NULL, client_pc_id VARCHAR(14) NOT NULL, fc_cbr_id VARCHAR(14) NOT NULL, balance BIGINT NOT NULL, credit_limit BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_transaction_date DATE NOT NULL, last_transaction_time TIME(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_45AF18B6772DFF6A ON clients_info (fc_cbr_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_45AF18B6BF3967502C0ECFAA772DFF6A ON clients_info (id, client_pc_id, fc_cbr_id)');
        $this->addSql('COMMENT ON COLUMN clients_info.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients_info.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN clients_info.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN clients_info.last_transaction_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN clients_info.last_transaction_time IS \'(DC2Type:time_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE clients_info');
    }
}
