<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120115531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add manager_id';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE companies ADD manager_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN companies.manager_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE companies ADD CONSTRAINT FK_8244AA3A783E3463 FOREIGN KEY (manager_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8244AA3A783E3463 ON companies (manager_id)');
        $this->addSql('ALTER TABLE users ADD manager_1c_id VARCHAR(10) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE users DROP manager_1c_id');
        $this->addSql('ALTER TABLE companies DROP CONSTRAINT FK_8244AA3A783E3463');
        $this->addSql('DROP INDEX IDX_8244AA3A783E3463');
        $this->addSql('ALTER TABLE companies DROP manager_id');
    }
}
