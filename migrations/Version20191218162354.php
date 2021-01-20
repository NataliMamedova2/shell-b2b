<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191218162354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify invoices_items quantity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        try {
            $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
        } catch (DBALException $e) {
        }

        $this->addSql('ALTER TABLE invoices_items ALTER quantity TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE invoices_items ALTER quantity DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE invoices_items ALTER quantity TYPE INT');
        $this->addSql('ALTER TABLE invoices_items ALTER quantity DROP DEFAULT');
    }
}
