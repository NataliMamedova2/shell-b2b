<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191119185815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify card_transactions table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_fa95b449499dbe53');
        $this->addSql('ALTER TABLE card_transactions ALTER post_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE card_transactions ALTER post_date DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN card_transactions.post_date IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE card_transactions ALTER post_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE card_transactions ALTER post_date DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN card_transactions.post_date IS NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_fa95b449499dbe53 ON card_transactions (client_1c_id)');
    }
}
