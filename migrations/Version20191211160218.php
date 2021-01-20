<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191211160218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create invoices_items table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('
                CREATE TABLE invoices_items
                (
                    id          UUID                           NOT NULL,
                    invoice_id  UUID     DEFAULT NULL,
                    line_number SMALLINT DEFAULT 0             NOT NULL,
                    fuel_code   VARCHAR(255)                   NOT NULL,
                    price       INT                            NOT NULL,
                    quantity    INT                            NOT NULL,
                    created_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                    PRIMARY KEY (id)
                )
        ');
        $this->addSql('CREATE INDEX IDX_D80DCB6A2989F1FD ON invoices_items (invoice_id)');
        $this->addSql('COMMENT ON COLUMN invoices_items.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoices_items.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoices_items.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invoices_items.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'ALTER TABLE invoices_items ADD CONSTRAINT FK_D80DCB6A2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE invoices_items');
    }
}
