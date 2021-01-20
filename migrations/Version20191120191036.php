<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120191036 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE shell_information (id UUID NOT NULL, full_name VARCHAR(255) NOT NULL, zkpo VARCHAR(255) NOT NULL, ipn VARCHAR(255) NOT NULL, certificate_number VARCHAR(255) NOT NULL, telephone_number VARCHAR(255) NOT NULL, post_address VARCHAR(255) NOT NULL, current_account VARCHAR(255) NOT NULL, current_mfo VARCHAR(255) NOT NULL, current_bank VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, site VARCHAR(255) DEFAULT NULL, nds VARCHAR(255) NOT NULL, invoice_valid_until_const VARCHAR(255) NOT NULL, invoice_prename_const VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FB5A9C59BF396750 ON shell_information (id)');
        $this->addSql('COMMENT ON COLUMN shell_information.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shell_information.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE shell_information');
    }
}
