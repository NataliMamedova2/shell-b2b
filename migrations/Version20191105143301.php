<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191105143301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tables "clients" & "clients_contracts"';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE clients (id UUID NOT NULL, client_1c_id VARCHAR(10) NOT NULL, full_name VARCHAR(164) NOT NULL, type SMALLINT DEFAULT 0 NOT NULL, nkt_id BIGINT NOT NULL, manager1cid VARCHAR(10) NOT NULL, agent1cid VARCHAR(10) NOT NULL, fc_cbr_id INT NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C82E74499DBE53 ON clients (client_1c_id)');
        $this->addSql('CREATE INDEX IDX_C82E74499DBE53 ON clients (client_1c_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C82E74BF396750499DBE53 ON clients (id, client_1c_id)');
        $this->addSql('COMMENT ON COLUMN clients.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN clients.updated_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('CREATE TABLE clients_contracts (id UUID NOT NULL, client_1c_id VARCHAR(10) NOT NULL, eck_dsg_ca SMALLINT NOT NULL, dsg_ca_ghb BIGINT NOT NULL, fixed_sum BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_581E283F499DBE53 ON clients_contracts (client_1c_id)');
        $this->addSql('CREATE INDEX IDX_581E283F499DBE53 ON clients_contracts (client_1c_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_581E283FBF396750499DBE53 ON clients_contracts (id, client_1c_id)');
        $this->addSql('COMMENT ON COLUMN clients_contracts.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients_contracts.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN clients_contracts.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE clients_contracts');
        $this->addSql('DROP TABLE clients');
    }
}
