<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200103084905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update card_transactions';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_fa95b449bf3967504bc3f2a4');
        $this->addSql('DROP INDEX idx_fa95b4494bc3f2a4');
        $this->addSql('CREATE INDEX idx_card_transactions_post_date ON card_transactions (post_date)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX idx_card_transactions_post_date');
        $this->addSql('CREATE UNIQUE INDEX uniq_fa95b449bf3967504bc3f2a4 ON card_transactions (id, transaction_1c_id)');
        $this->addSql('CREATE INDEX idx_fa95b4494bc3f2a4 ON card_transactions (transaction_1c_id)');
    }
}
