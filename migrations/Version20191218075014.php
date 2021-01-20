<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191218075014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create export table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE export
                (
                    id               UUID                           NOT NULL,
                    type             VARCHAR(255)                   NOT NULL,
                    file_name        VARCHAR(255)                   NOT NULL,
                    file_extension   VARCHAR(3)                     NOT NULL,
                    file_size        INT                            NOT NULL,
                    file_source_path VARCHAR(255)                   NOT NULL,
                    created_at       TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                    PRIMARY KEY (id)
                )'
        );
        $this->addSql('COMMENT ON COLUMN export.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN export.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE export');
    }
}
