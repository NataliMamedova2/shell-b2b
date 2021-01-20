<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191223091046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE company_register_request DROP contract_number');
        $this->addSql('ALTER TABLE company_register_request DROP contract_date');
        $this->addSql('ALTER TABLE companies DROP contract_number');
        $this->addSql('ALTER TABLE companies DROP contract_date');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE company_register_request ADD contract_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company_register_request ADD contract_date DATE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN company_register_request.contract_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE companies ADD contract_number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE companies ADD contract_date DATE NOT NULL');
        $this->addSql('COMMENT ON COLUMN companies.contract_date IS \'(DC2Type:date_immutable)\'');
    }
}
