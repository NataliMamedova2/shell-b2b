<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200103091224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create VIEW view_company_transactions';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("
            CREATE VIEW view_company_transactions AS
                SELECT r.id              as id,
                       NULL::varchar     as client_1c_id,
                       r.fc_cbr_id       as fc_cbr_id,
                       r.amount::bigint  as amount,
                       r.operation_date  as date,
                       'refill'::varchar as type
                FROM refill_balance r
                UNION ALL
                SELECT d.id                   as id,
                       d.client_1c_id         as client_1c_id,
                       NULL::varchar          as fc_cbr_id,
                       d.discount_sum::bigint as amount,
                       d.operation_date       as date,
                       'discount'::varchar    as type
                FROM discounts d
                UNION ALL
                SELECT card_transactions.id                                                                      as id,
                       card_transactions.client_1c_id                                                            as client_1c_id,
                       NULL::varchar                                                                             as fc_cbr_id,
                       sum(card_transactions.debit)::bigint                                                      as amount,
                       ((date_trunc('day', days.d) + INTERVAL '1 DAY' - INTERVAL '1 SECOND') - INTERVAL '1 DAY') as date,
                       'write-off-cards'::varchar                                                                as type
                FROM (
                         SELECT min(post_date), max(post_date)
                         FROM card_transactions as t
                     ) AS minmax
                         CROSS JOIN LATERAL generate_series(minmax.min, minmax.max, '1 DAY')
                    AS days(d)
                         LEFT OUTER JOIN card_transactions ON post_date >= days.d
                    AND post_date < (days.d + '1 DAY')
                WHERE id IS NOT NULL
                GROUP BY days.d, id;
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP VIEW view_company_transactions;');
    }
}
