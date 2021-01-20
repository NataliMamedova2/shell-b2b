<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191121114304 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX idx_6e620757e4af4c20');
        $this->addSql('DROP INDEX uniq_6e620757e4af4c20');
        $this->addSql('DROP INDEX uniq_6e620757bf396750e4af4c20');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6E620757BF396750 ON fuel_cards (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_6E620757BF396750');
        $this->addSql('CREATE INDEX idx_6e620757e4af4c20 ON fuel_cards (card_number)');
        $this->addSql('CREATE UNIQUE INDEX uniq_6e620757e4af4c20 ON fuel_cards (card_number)');
        $this->addSql('CREATE UNIQUE INDEX uniq_6e620757bf396750e4af4c20 ON fuel_cards (id, card_number)');
    }
}
