<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191226092204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unique email in company_register_request';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX idx_5500c83ae7927c74');
        $this->addSql('DROP INDEX uniq_5500c83ae7927c74');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE INDEX idx_5500c83ae7927c74 ON company_register_request (email)');
        $this->addSql('CREATE UNIQUE INDEX uniq_5500c83ae7927c74 ON company_register_request (email)');
    }
}
