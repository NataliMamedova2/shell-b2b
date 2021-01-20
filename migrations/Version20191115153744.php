<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191115153744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix clients table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE clients ADD manager_1c_id VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE clients ADD agent_1c_id VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE clients DROP manager1cid');
        $this->addSql('ALTER TABLE clients DROP agent1cid');
        $this->addSql('ALTER TABLE clients ALTER full_name TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE clients ALTER fc_cbr_id TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE clients ALTER fc_cbr_id DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE clients ADD manager1cid VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE clients ADD agent1cid VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE clients DROP manager_1c_id');
        $this->addSql('ALTER TABLE clients DROP agent_1c_id');
        $this->addSql('ALTER TABLE clients ALTER full_name TYPE VARCHAR(164)');
        $this->addSql('ALTER TABLE clients ALTER fc_cbr_id TYPE INT');
        $this->addSql('ALTER TABLE clients ALTER fc_cbr_id DROP DEFAULT');
        $this->addSql('ALTER TABLE clients ALTER fc_cbr_id TYPE INT');
    }
}
