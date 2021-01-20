<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191125132435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify card_transactions table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE card_transactions ALTER fuel_quantity TYPE BIGINT USING fuel_quantity::bigint');
        $this->addSql('ALTER TABLE card_transactions ALTER fuel_quantity DROP DEFAULT');
        $this->addSql('ALTER TABLE card_transactions ALTER stella_price TYPE BIGINT USING stella_price::bigint');
        $this->addSql('ALTER TABLE card_transactions ALTER stella_price DROP DEFAULT');
        $this->addSql('ALTER TABLE card_transactions ALTER debit TYPE BIGINT USING debit::bigint');
        $this->addSql('ALTER TABLE card_transactions ALTER debit DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE card_transactions ALTER fuel_quantity TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE card_transactions ALTER fuel_quantity DROP DEFAULT');
        $this->addSql('ALTER TABLE card_transactions ALTER stella_price TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE card_transactions ALTER stella_price DROP DEFAULT');
        $this->addSql('ALTER TABLE card_transactions ALTER debit TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE card_transactions ALTER debit DROP DEFAULT');
    }
}
