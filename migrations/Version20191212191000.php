<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191212191000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create refill_balance table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE refill_balance (id UUID NOT NULL, card_owner INT NOT NULL, fc_cbr_id VARCHAR(255) NOT NULL, operation INT NOT NULL, amount VARCHAR(255) NOT NULL, operation_date DATE NOT NULL, operation_time TIME(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON COLUMN refill_balance.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN refill_balance.operation_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN refill_balance.operation_time IS \'(DC2Type:time_immutable)\'');
        $this->addSql('COMMENT ON COLUMN refill_balance.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql('DROP TABLE refill_balance');
    }
}
