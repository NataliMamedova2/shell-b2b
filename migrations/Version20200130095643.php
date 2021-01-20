<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200130095643 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Remove duplicates from refill_balance table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('
            DELETE
                FROM refill_balance
                WHERE id NOT IN (
                    SELECT id
                    FROM (SELECT rfb.id,
                                 rfb.card_owner,
                                 rfb.fc_cbr_id,
                                 rfb.operation,
                                 rfb.amount,
                                 rfb.operation_date,
                                 ROW_NUMBER() OVER
                                     (PARTITION BY (rfb.card_owner, rfb.fc_cbr_id, rfb.operation, rfb.amount,
                                                    rfb.operation_date) ORDER BY rfb.created_at DESC) rn
                          FROM refill_balance rfb
                         ) tmp
                    WHERE rn = 1);
            ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
    }
}
