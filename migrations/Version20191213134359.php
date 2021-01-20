<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191213134359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify invoices table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE invoices ALTER amount TYPE BIGINT USING amount::bigint');
        $this->addSql('ALTER TABLE invoices ALTER amount DROP DEFAULT');
        $this->addSql('ALTER TABLE invoices_items ALTER price TYPE BIGINT USING price::bigint');
        $this->addSql('ALTER TABLE invoices_items ALTER price DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE invoices ALTER amount TYPE INT');
        $this->addSql('ALTER TABLE invoices ALTER amount DROP DEFAULT');
        $this->addSql('ALTER TABLE invoices_items ALTER price TYPE INT');
        $this->addSql('ALTER TABLE invoices_items ALTER price DROP DEFAULT');
    }
}
