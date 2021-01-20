<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200220125148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE companies DROP CONSTRAINT fk_8244aa3a783e3463');
        $this->addSql('DROP INDEX idx_8244aa3a783e3463');
        $this->addSql('ALTER TABLE companies DROP manager_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE companies ADD manager_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN companies.manager_id IS \'(DC2Type:uuid)\'');
        $this->addSql(
            'ALTER TABLE companies ADD CONSTRAINT fk_8244aa3a783e3463 FOREIGN KEY (manager_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('CREATE INDEX idx_8244aa3a783e3463 ON companies (manager_id)');
    }
}
