<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191114102037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create company_register_request table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE company_register_request (id UUID NOT NULL, client_id UUID NOT NULL, email VARCHAR(255) NOT NULL, contract_number VARCHAR(255) NOT NULL, contract_date DATE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, token VARCHAR(255) DEFAULT NULL, expire TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5500C83AE7927C74 ON company_register_request (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5500C83A5F37A13B ON company_register_request (token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5500C83A19EB6921 ON company_register_request (client_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5500C83AE7927C7419EB6921 ON company_register_request (email, client_id)');
        $this->addSql('COMMENT ON COLUMN company_register_request.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN company_register_request.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN company_register_request.contract_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN company_register_request.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN company_register_request.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN company_register_request.expire IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE company_register_request ADD CONSTRAINT FK_5500C83A19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE company_register_request');
    }
}
