<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201206210423 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE partner_transactions ALTER write_off_type TYPE INT USING write_off_type::integer');
        $this->addSql('ALTER TABLE partner_transactions ALTER write_off_type DROP DEFAULT');

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE partner_transactions ALTER write_off_type TYPE BOOLEAN');
        $this->addSql('ALTER TABLE partner_transactions ALTER write_off_type DROP DEFAULT');
    }
}
