<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191015091927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create file_storage table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE files_storage (id UUID NOT NULL, file_name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, extension VARCHAR(5) NOT NULL, original_name VARCHAR(255) NOT NULL, type VARCHAR(40) NOT NULL, size INT NOT NULL, meta_info JSONB DEFAULT NULL, uploaded_ip VARCHAR(50) NOT NULL, uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN files_storage.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN files_storage.uploaded_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE files_storage');
    }
}
