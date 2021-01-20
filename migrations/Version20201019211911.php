<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201019211911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create partners table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE partners (id UUID NOT NULL, title VARCHAR(25) NOT NULL, client_1C_id VARCHAR(14) NOT NULL, 
                 created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                 manager_1C_id VARCHAR(14) NOT NULL, edrpou VARCHAR(15) DEFAULT NULL, emitent_number VARCHAR(4) NOT NULL,
                 PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_45AF18B6772DJK6A ON partners (emitent_number)');
       // $this->addSql('CREATE UNIQUE INDEX UNIQ_45AF18B6BF3967502C0ECFAA772DFF6A ON clients_info (id, client_pc_id, fc_cbr_id)');
       // $this->addSql('COMMENT ON COLUMN clients_info.id IS \'(DC2Type:uuid)\'');
       // $this->addSql('COMMENT ON COLUMN clients_info.created_at IS \'(DC2Type:datetime_immutable)\'');
       // $this->addSql('COMMENT ON COLUMN clients_info.updated_at IS \'(DC2Type:datetime_immutable)\'');
       // $this->addSql('COMMENT ON COLUMN clients_info.last_transaction_date IS \'(DC2Type:date_immutable)\'');
       // $this->addSql('COMMENT ON COLUMN clients_info.last_transaction_time IS \'(DC2Type:time_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE partners');
        $this->addSql('DROP INDEX IDX_45AF18B6772DJK6A');
    }
}
