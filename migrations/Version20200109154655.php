<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200109154655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create view_transactions_network_stations view';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('
            CREATE VIEW view_transactions_network_stations as
                WITH transaction_view (client_1c_id, azs_code, azs_name, duplicate_count)
                         AS
                         (
                             SELECT
                                    client_1c_id,
                                    azs_code,
                                    azs_name,
                                    ROW_NUMBER()
                                    OVER (PARTITION BY client_1c_id, azs_code) AS duplicate_count
                             FROM card_transactions
                         )
                SELECT
                       azs_code as code,
                       client_1c_id,
                       azs_name as name
                FROM transaction_view
                WHERE duplicate_count = 1;
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP VIEW view_transactions_network_stations');
    }
}
