<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200127135221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove old fields';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE refill_balance DROP operation_time');
        $this->addSql('ALTER TABLE refill_balance DROP amount_old');
        $this->addSql('ALTER TABLE refill_balance DROP operation_date_old');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE refill_balance ADD operation_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE refill_balance ADD amount_old VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE refill_balance ADD operation_date_old DATE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN refill_balance.operation_time IS \'(DC2Type:time_immutable)\'');
    }
}
