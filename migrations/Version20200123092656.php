<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200123092656 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_3390d69abf396750');
        $this->addSql('alter table fuel_types drop constraint fuel_types_pkey;');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3390D69A676A6889 ON fuel_types (fuel_code)');
        $this->addSql('ALTER TABLE fuel_types ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX UNIQ_3390D69A676A6889');
        $this->addSql('DROP INDEX fuel_types_pkey');
        $this->addSql('CREATE UNIQUE INDEX uniq_3390d69abf396750 ON fuel_types (id)');
        $this->addSql('ALTER TABLE fuel_types ADD PRIMARY KEY (fuel_code)');
    }
}
