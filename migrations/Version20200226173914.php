<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200226173914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cards ADD driver_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN cards.driver_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE cards ADD CONSTRAINT FK_4C258FDC3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4C258FDC3423909 ON cards (driver_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cards DROP CONSTRAINT FK_4C258FDC3423909');
        $this->addSql('DROP INDEX IDX_4C258FDC3423909');
        $this->addSql('ALTER TABLE cards DROP driver_id');
    }
}
