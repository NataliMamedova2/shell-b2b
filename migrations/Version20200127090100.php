<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200127090100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify view_company_transactions view';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            "
            CREATE OR REPLACE VIEW view_company_transactions AS
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
                SELECT uuid_generate_v4()                                                     as id,
                       card_transactions.client_1c_id                                         as client_1c_id,
                       NULL::varchar                                                          as fc_cbr_id,
                       sum(CASE
                               WHEN card_transactions.write_off_type::int = 0
                                   THEN card_transactions.debit
                               ELSE (- card_transactions.debit) END)::bigint                  as amount,
                       ((date_trunc('day', days.d) + INTERVAL '1 DAY' - INTERVAL '1 SECOND')) as date,
                       'write-off-cards'::varchar                                             as type
                FROM (
                         SELECT cast(min(post_date) as date),
                                cast(max(post_date) as date)
                         FROM card_transactions as t
                     ) AS minmax
                         CROSS JOIN LATERAL generate_series(minmax.min, minmax.max, '1 DAY')
                    AS days(d)
                         LEFT OUTER JOIN card_transactions ON post_date >= days.d
                    AND post_date < (days.d + '1 DAY') AND post_date < now()::date
                GROUP BY days.d, client_1c_id;
        "
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            "
            CREATE OR REPLACE VIEW view_company_transactions AS
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
                SELECT uuid_generate_v4()                                                     as id,
                       card_transactions.client_1c_id                                         as client_1c_id,
                       NULL::varchar                                                          as fc_cbr_id,
                       sum(card_transactions.debit)::bigint                                   as amount,
                       ((date_trunc('day', days.d) + INTERVAL '1 DAY' - INTERVAL '1 SECOND')) as date,
                       'write-off-cards'::varchar                                             as type
                FROM (
                         SELECT
                             cast(min(post_date) as date),
                             cast(max(post_date) as date)
                         FROM card_transactions as t
                     ) AS minmax
                         CROSS JOIN LATERAL generate_series(minmax.min, minmax.max, '1 DAY')
                    AS days(d)
                         LEFT OUTER JOIN card_transactions ON post_date >= days.d
                    AND post_date < (days.d + '1 DAY') AND post_date < now()::date
                GROUP BY days.d, client_1c_id;
        "
        );
    }
}
