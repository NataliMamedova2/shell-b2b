<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200214072501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify clients_info table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE clients_info ADD client_pc_id_old VARCHAR(14) DEFAULT NULL;');
        $this->addSql('ALTER TABLE clients_info ADD fc_cbr_id_old VARCHAR(14) DEFAULT NULL;');
        $this->addSql('ALTER TABLE clients_info ADD balance_old DOUBLE PRECISION DEFAULT NULL;');
        $this->addSql('ALTER TABLE clients_info ADD credit_limit_old BIGINT DEFAULT NULL;');

        $this->addSql('UPDATE clients_info SET client_pc_id_old = client_pc_id;');
        $this->addSql('UPDATE clients_info SET fc_cbr_id_old = fc_cbr_id;');
        $this->addSql('UPDATE clients_info SET balance_old = balance;');
        $this->addSql('UPDATE clients_info SET credit_limit_old = credit_limit;');

        $this->addSql('ALTER TABLE clients_info ALTER client_pc_id TYPE BIGINT USING client_pc_id::bigint');
        $this->addSql('ALTER TABLE clients_info ALTER client_pc_id DROP DEFAULT');
        $this->addSql('ALTER TABLE clients_info ALTER balance TYPE BIGINT USING balance::bigint');
        $this->addSql('ALTER TABLE clients_info ALTER balance DROP DEFAULT');

        $this->addSql('UPDATE clients_info SET fc_cbr_id = fc_cbr_id_old::bigint;');
        $this->addSql('ALTER TABLE clients_info ALTER fc_cbr_id TYPE VARCHAR(10)');

        $this->addSql('UPDATE clients_info SET balance = (balance_old::bigint * 100);');
        $this->addSql('UPDATE clients_info SET credit_limit = (credit_limit_old::bigint * 100);');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE clients_info ALTER client_pc_id TYPE VARCHAR(14)');
        $this->addSql('ALTER TABLE clients_info ALTER client_pc_id DROP DEFAULT');
        $this->addSql('ALTER TABLE clients_info ALTER fc_cbr_id TYPE VARCHAR(14)');
        $this->addSql('ALTER TABLE clients_info ALTER balance TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE clients_info ALTER balance DROP DEFAULT');

        $this->addSql('UPDATE clients_info SET client_pc_id = client_pc_id_old;');
        $this->addSql('UPDATE clients_info SET fc_cbr_id = fc_cbr_id_old;');
        $this->addSql('UPDATE clients_info SET balance = balance_old;');
        $this->addSql('UPDATE clients_info SET credit_limit = credit_limit_old;');

        $this->addSql('ALTER TABLE clients_info DROP client_pc_id_old;');
        $this->addSql('ALTER TABLE clients_info DROP fc_cbr_id_old;');
        $this->addSql('ALTER TABLE clients_info DROP balance_old;');
        $this->addSql('ALTER TABLE clients_info DROP credit_limit_old;');
    }
}
