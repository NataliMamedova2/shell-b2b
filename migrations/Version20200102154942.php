<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200102154942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ALTER refill_balance table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE refill_balance ADD amount_old VARCHAR DEFAULT NULL;');
        $this->addSql('UPDATE refill_balance SET amount_old = amount;');
        $this->addSql('UPDATE refill_balance SET amount = REGEXP_REPLACE(amount, \'(^|-)0*\', \'\')');
        $this->addSql('ALTER TABLE refill_balance ALTER COLUMN amount TYPE BIGINT USING amount::BIGINT;');
        $this->addSql('ALTER TABLE refill_balance ALTER amount SET NOT NULL');

        $this->addSql('ALTER TABLE refill_balance ADD operation_date_old DATE DEFAULT NULL;');
        $this->addSql('UPDATE refill_balance SET operation_date_old = operation_date;');
        $this->addSql('ALTER TABLE refill_balance ALTER COLUMN operation_time DROP NOT NULL;');

        $this->addSql('ALTER TABLE refill_balance ALTER COLUMN operation_date TYPE TIMESTAMP USING operation_date::timestamp;');
        $this->addSql('COMMENT ON COLUMN refill_balance.operation_date IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('UPDATE refill_balance SET operation_date = (operation_date_old || \' \' || operation_time)::timestamp');
        $this->addSql('ALTER TABLE refill_balance ALTER operation_date SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE refill_balance DROP COLUMN amount;');
        $this->addSql('ALTER TABLE refill_balance RENAME COLUMN amount_old TO amount;');
        $this->addSql('ALTER TABLE refill_balance ALTER amount DROP NOT NULL');

        $this->addSql('ALTER TABLE refill_balance DROP COLUMN operation_date;');
        $this->addSql('ALTER TABLE refill_balance RENAME COLUMN operation_date_old TO operation_date;');
        $this->addSql('ALTER TABLE refill_balance ALTER operation_date DROP NOT NULL');
    }
}
