<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201206204100 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE partner_transactions (id UUID NOT NULL, transaction_1c_id VARCHAR(255) NOT NULL, 
        client_1c_id VARCHAR(255) NOT NULL, card_number VARCHAR(255) NOT NULL, fuel_code VARCHAR(255) NOT NULL,
        fuel_quantity VARCHAR(255) NOT NULL, stella_price VARCHAR(255) NOT NULL, debit VARCHAR(255) NOT NULL,
        azs_code VARCHAR(255) NOT NULL, azs_name VARCHAR(255) NOT NULL, region_code VARCHAR(255) NOT NULL, 
        region_name VARCHAR(255) NOT NULL, post_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, write_off_type BOOLEAN NOT NULL, 
        created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
        updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FA95B6694BC3F2A4 ON partner_transactions (transaction_1c_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FA95B669499DBE53 ON partner_transactions (client_1c_id)');
        $this->addSql('CREATE INDEX IDX_FA95B6694BC3F2A4 ON partner_transactions (transaction_1c_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FA95B449BF7587504BC3F2A4 ON partner_transactions (id, transaction_1c_id)');
        $this->addSql('COMMENT ON COLUMN card_transactions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN card_transactions.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN card_transactions.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE partner_transactions');
    }
}
