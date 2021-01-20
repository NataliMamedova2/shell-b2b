<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201129213000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create partner_users table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE partner_users (id UUID NOT NULL, email VARCHAR(255) NOT NULL, 
        username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, 
        first_name  VARCHAR(35) NOT NULL, 
        middle_name varchar(35) NOT NULL,
        last_name varchar(35) NOT NULL,
        phone VARCHAR(13) DEFAULT NULL, 
        partner_id UUID NOT NULL,
        roles JSONB NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, 
        last_logged_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
        created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
        updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7874C74 ON partner_users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0744 ON partner_users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74F85E0744 ON partner_users (email, username)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE partner_users');
    }
}
