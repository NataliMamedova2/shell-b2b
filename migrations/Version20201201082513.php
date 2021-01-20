<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201201082513 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE partners_info DROP CONSTRAINT  IF EXISTS FK_5377028C945B1AD6');
        $this->addSql('ALTER TABLE partners_info ADD CONSTRAINT FK_5377028C945B1AD6 FOREIGN KEY (partner_id) REFERENCES partners (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE partners_info DROP CONSTRAINT FK_5377028C945B1AD6');
        $this->addSql('ALTER TABLE partners_info ADD CONSTRAINT FK_5377028C945B1AD6 FOREIGN KEY (partner_id) REFERENCES partners (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
