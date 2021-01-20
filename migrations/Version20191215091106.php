<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191215091106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify invoices';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE invoices DROP updated_at');
        $this->addSql('ALTER TABLE invoices DROP amount');
        $this->addSql('ALTER TABLE invoices_items DROP updated_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE invoices ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE invoices ADD amount VARCHAR(255) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN invoices.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE invoices_items ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN invoices_items.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }
}
