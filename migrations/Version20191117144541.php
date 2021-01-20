<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191117144541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create companies table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE company_users (id UUID NOT NULL, company_id UUID NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(13) DEFAULT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, last_logged_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5372078CF85E0677 ON company_users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5372078CE7927C74 ON company_users (email)');
        $this->addSql('CREATE INDEX IDX_5372078C979B1AD6 ON company_users (company_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5372078CE7927C74F85E0677 ON company_users (email, username)');
        $this->addSql('COMMENT ON COLUMN company_users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN company_users.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN company_users.last_logged_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN company_users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN company_users.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE companies (id UUID NOT NULL, client_id UUID DEFAULT NULL, name VARCHAR(500) NOT NULL, email VARCHAR(255) NOT NULL, postal_address VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, contract_number VARCHAR(255) NOT NULL, contract_date DATE NOT NULL, accounting_email VARCHAR(255) DEFAULT NULL, accounting_phone VARCHAR(13) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_8244AA3A19EB6921 ON companies (client_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8244AA3A19EB6921 ON companies (client_id)');
        $this->addSql('COMMENT ON COLUMN companies.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN companies.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN companies.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN companies.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN companies.contract_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE company_users ADD CONSTRAINT FK_5372078C979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE companies ADD CONSTRAINT FK_8244AA3A19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('DROP TABLE client_users');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE company_users DROP CONSTRAINT FK_5372078C979B1AD6');
        $this->addSql('CREATE TABLE client_users (id UUID NOT NULL, client_id UUID NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(13) DEFAULT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, last_logged_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX uniq_b7ee395ee7927c74 ON client_users (email)');
        $this->addSql('CREATE INDEX idx_b7ee395e19eb6921 ON client_users (client_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_b7ee395ee7927c74f85e0677 ON client_users (email, username)');
        $this->addSql('CREATE UNIQUE INDEX uniq_b7ee395ef85e0677 ON client_users (username)');
        $this->addSql('COMMENT ON COLUMN client_users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN client_users.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN client_users.last_logged_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN client_users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN client_users.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE client_users ADD CONSTRAINT fk_b7ee395e19eb6921 FOREIGN KEY (client_id) REFERENCES clients (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('DROP TABLE company_users');
        $this->addSql('DROP TABLE companies');
    }
}
