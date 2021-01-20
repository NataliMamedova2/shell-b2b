<?php

namespace App\Clients\Infrastructure\Discount\Repository;

use App\Clients\Domain\Client\Client;
use Doctrine\ORM\NonUniqueResultException;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Repository\Repository;
use Infrastructure\Repository\DoctrineRepository;

final class DiscountRepository extends DoctrineRepository implements Repository
{
    /**
     * @param Client             $client
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     *
     * @return int
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function calculateSum(
        Client $client,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int {
        $queryBuilder = $this->getQueryBuilder();
        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->select("SUM({$alias}.discountSum)");

        $spec = Spec::andX(
            Spec::eq('client1CId', $client->getClient1CId()),
            Spec::gte('operationDate', $startDate),
            Spec::lte('operationDate', $endDate)
        );
        $queryBuilder->andWhere($spec->getFilter($queryBuilder, $alias));

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function calculateClientDebitSumByMonths(
        string $client1CId,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): array {

        $sql = '
            SELECT
                date_trunc(\'month\', d.operation_date)::date AS date,
                sum(d.discount_sum) as sum
            FROM discounts as d
                WHERE d.client_1c_id = :clientId
                    AND d.operation_date >= :startDate
                    AND d.operation_date <= :endDate
            GROUP BY date_trunc(\'month\', d.operation_date)::date;
        ';

        $entityManager = $this->getQueryBuilder()
            ->getEntityManager();
        $connection = $entityManager
            ->getConnection();

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'clientId' => $client1CId,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);

        $result = $stmt->fetchAll();

        $codes = [];
        foreach ($result as $item) {
            $codes[$item['date']] = $item['sum'];
        }

        return $codes;
    }
}
