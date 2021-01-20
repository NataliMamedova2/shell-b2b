<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201124090343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create clients_info table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE partners_info (id UUID NOT NULL, partner_id UUID NOT NULL, balance BIGINT NOT NULL, credit_limit BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_transaction_date DATE NOT NULL, last_transaction_time TIME(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );

        $this->addSql('COMMENT ON COLUMN partners_info.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN partners_info.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN partners_info.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN partners_info.last_transaction_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN partners_info.last_transaction_time IS \'(DC2Type:time_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE partners_info');
    }
}
