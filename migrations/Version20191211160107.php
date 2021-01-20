<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191211160107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create invoices table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE invoices
                (
                    id              UUID                           NOT NULL,
                    client_1c_id    VARCHAR(255)                   NOT NULL,
                    invoice_id      VARCHAR(255)                   NOT NULL,
                    number          VARCHAR(255)                   NOT NULL,
                    amount          INT                            NOT NULL,
                    value_tax       INT                            NOT NULL,
                    creation_date   DATE                           NOT NULL,
                    expiration_date DATE                           NOT NULL,
                    export_status   SMALLINT DEFAULT 0             NOT NULL,
                    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                    PRIMARY KEY (id)
                )'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A2F2F9596901F54 ON invoices (number)');
        $this->addSql('COMMENT ON COLUMN invoices.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoices.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invoices.creation_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invoices.expiration_date IS \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE invoices');
    }
}
