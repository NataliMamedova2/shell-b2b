<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201201101124 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE partners_balance_history DROP COLUMN IF EXISTS partner_id ');
        $this->addSql('ALTER TABLE partners_balance_history ADD COLUMN partner_info_id UUID NOT NULL');
        $this->addSql('ALTER TABLE partners_balance_history DROP CONSTRAINT IF EXISTS FK_9837028C979B1AD6');
        $this->addSql('ALTER TABLE partners_balance_history ADD CONSTRAINT FK_9837028C979B1AD6 FOREIGN KEY (partner_info_id) REFERENCES partners_info (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE partners_balance_history DROP CONSTRAINT FK_9837028C979B1AD6');
    }
}
