<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191124161203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE companies ALTER name DROP NOT NULL');
        $this->addSql('TRUNCATE TABLE feedback;');
        $this->addSql('ALTER TABLE feedback ADD user_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN feedback.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D2294458A76ED395 FOREIGN KEY (user_id) REFERENCES company_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D2294458A76ED395 ON feedback (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE feedback DROP CONSTRAINT FK_D2294458A76ED395');
        $this->addSql('DROP INDEX IDX_D2294458A76ED395');
        $this->addSql('ALTER TABLE feedback DROP user_id');
        $this->addSql('ALTER TABLE companies ALTER name SET NOT NULL');
    }
}
