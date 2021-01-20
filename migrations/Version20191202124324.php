<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191202124324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create cards_order table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE cards_order (id UUID NOT NULL, user_id UUID NOT NULL, count INT NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(13) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_5337F98A76ED395 ON cards_order (user_id)');
        $this->addSql('COMMENT ON COLUMN cards_order.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cards_order.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cards_order.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'ALTER TABLE cards_order ADD CONSTRAINT FK_5337F98A76ED395 FOREIGN KEY (user_id) REFERENCES company_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE cards_order');
    }
}
