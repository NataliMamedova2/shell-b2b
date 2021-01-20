<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191111152124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create client_users';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE client_users (id UUID NOT NULL, client_id UUID NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(13) DEFAULT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, last_logged_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B7EE395EF85E0677 ON client_users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B7EE395EE7927C74 ON client_users (email)');
        $this->addSql('CREATE INDEX IDX_B7EE395E19EB6921 ON client_users (client_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B7EE395EE7927C74F85E0677 ON client_users (email, username)');
        $this->addSql('COMMENT ON COLUMN client_users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN client_users.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN client_users.last_logged_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN client_users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN client_users.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE client_users ADD CONSTRAINT FK_B7EE395E19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE client_users');
    }
}
