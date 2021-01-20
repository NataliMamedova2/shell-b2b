<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201123205855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE partners_balance_history (id UUID NOT NULL, partner_id UUID NOT NULL, balance BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_L1G86A4K2C0ECFAA ON partners_balance_history (partner_id)');
        // $this->addSql('ALTER TABLE partners_balance_history ADD CONSTRAINT fk_partner FOREIGN KEY(partner_id) REFERENCES partners (id)');
        $this->addSql('COMMENT ON COLUMN partners_balance_history.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN partners_balance_history.partner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN partners_balance_history.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
        // $this->addSql('ALTER TABLE partners_balance_history DROP CONSTRAINT fk_partner');
        $this->addSql('DROP TABLE partners_balance_history');
        $this->addSql('DROP INDEX IDX_L1G86A4K2C0ECFAA');
    }
}
