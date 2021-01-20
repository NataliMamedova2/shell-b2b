<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191219134630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE discounts ALTER discount_sum TYPE BIGINT USING discount_sum::bigint');
        $this->addSql('ALTER TABLE discounts ALTER discount_sum DROP DEFAULT');
        $this->addSql('DROP INDEX uniq_fc5702b8499dbe53');
        $this->addSql('DROP INDEX idx_fc5702b8499dbe53');
        $this->addSql('DROP INDEX uniq_fc5702b8bf396750499dbe53');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE discounts ALTER discount_sum TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE discounts ALTER discount_sum DROP DEFAULT');
        $this->addSql('CREATE UNIQUE INDEX uniq_fc5702b8499dbe53 ON discounts (client_1c_id)');
        $this->addSql('CREATE INDEX idx_fc5702b8499dbe53 ON discounts (client_1c_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_fc5702b8bf396750499dbe53 ON discounts (id, client_1c_id)');
    }
}
