<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191115125018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE feedback DROP CONSTRAINT fk_d2294458a76ed395');
        $this->addSql('DROP INDEX idx_d2294458a76ed395');
        $this->addSql('ALTER TABLE feedback DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE feedback ADD user_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN feedback.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT fk_d2294458a76ed395 FOREIGN KEY (user_id) REFERENCES client_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d2294458a76ed395 ON feedback (user_id)');
    }
}
