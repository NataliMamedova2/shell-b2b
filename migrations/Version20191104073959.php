<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191104073959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create "import_files" table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE import_files (id VARCHAR(255) NOT NULL, import_id UUID DEFAULT NULL, file_name VARCHAR(255) NOT NULL, extension VARCHAR(255) NOT NULL, size INT NOT NULL, source_file_meta_data JSONB DEFAULT NULL, dest_file_meta_data JSONB DEFAULT NULL, crated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, start_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, elapsed VARCHAR(255) DEFAULT NULL, error_count INT DEFAULT NULL, success_count INT DEFAULT NULL, total_processed_count INT DEFAULT NULL, exceptions JSONB DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EB60EF97B6A263D9 ON import_files (import_id)');
        $this->addSql('COMMENT ON COLUMN import_files.import_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN import_files.crated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN import_files.start_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN import_files.end_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN import_files.elapsed IS \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE import_files ADD CONSTRAINT FK_EB60EF97B6A263D9 FOREIGN KEY (import_id) REFERENCES import (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE import_files');
    }
}
