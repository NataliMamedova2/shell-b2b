<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191215165720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify documents amount';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        try {
            $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
        } catch (DBALException $e) {
        }

        $this->addSql('ALTER TABLE documents ALTER amount TYPE BIGINT USING amount::bigint');
        $this->addSql('ALTER TABLE documents ALTER amount DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE documents ALTER amount TYPE INT');
        $this->addSql('ALTER TABLE documents ALTER amount DROP DEFAULT');
    }
}
