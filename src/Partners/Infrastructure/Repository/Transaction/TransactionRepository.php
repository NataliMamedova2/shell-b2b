<?php

namespace App\Partners\Infrastructure\Repository\Transaction;

use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Repository\Repository;
use Infrastructure\Repository\DoctrineRepository;
use Doctrine\Common\Collections\Criteria;
use Infrastructure\Criteria\CriteriaFactory;
use App\Partners\Domain\Partner\Partner;

final class TransactionRepository extends DoctrineRepository implements Repository
{
    public function calculateDebitSum(
        string $clientId,
        string $cardNumber,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int {
        $queryBuilder = $this->getQueryBuilder();
        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->select("SUM({$alias}.debit)");

        $spec = Spec::andX(
            Spec::eq('client1CId', $clientId),
            Spec::eq('cardNumber', $cardNumber),
            Spec::gte('postDate', $startDate),
            Spec::lte('postDate', $endDate)
        );
        $queryBuilder->andWhere($spec->getFilter($queryBuilder, $alias));

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Partner $partner
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function calculatePartnerDebitSum(
        Partner $partner,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int {
        $queryBuilder = $this->getQueryBuilder();
        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->select("SUM({$alias}.debit)");

        $spec = Spec::andX(
            Spec::eq('client1CId', $partner->getClient1CId()),
            Spec::gte('postDate', $startDate),
            Spec::lte('postDate', $endDate)
        );
        $queryBuilder->andWhere($spec->getFilter($queryBuilder, $alias));

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function calculateFuelQuantitySum(
        string $clientId,
        string $cardNumber,
        string $fuelCode,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int {
        $queryBuilder = $this->getQueryBuilder();
        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->select("SUM({$alias}.fuelQuantity)");

        $spec = Spec::andX(
            Spec::eq('client1CId', $clientId),
            Spec::eq('cardNumber', $cardNumber),
            Spec::eq('fuelCode', $fuelCode),
            Spec::gte('postDate', $startDate),
            Spec::lte('postDate', $endDate)
        );
        $queryBuilder->andWhere($spec->getFilter($queryBuilder, $alias));

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function calculateSumByTypeOnField(array $criteria, Type $type, string $field): int
    {
        $criteriaObj = $this->criteriaFactory->build($this->entityClass, $criteria);

        $queryBuilder = $criteriaObj->getQueryBuilder();
        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->select("SUM({$alias}.$field)");

        $spec = Spec::andX(
            Spec::eq('writeOffType', $type->getValue())
        );

        $queryBuilder->andWhere($spec->getFilter($queryBuilder, $alias));

        return $queryBuilder->getQuery()->getSingleScalarResult() ?? 0;
    }

    public function calculateSumReportOnField(array $criteria, string $field)
    {
        $writeOffSum = $this->calculateSumByTypeOnField($criteria, Type::writeOff(), $field);
        $returnSum = $this->calculateSumByTypeOnField($criteria, Type::return(), $field);

        return $writeOffSum - $returnSum;
    }

    public function getPartnerFuelCodes(Partner $partner): array
    {
        $sql = '
            SELECT 
                DISTINCT ON (c0_.fuel_code) c0_.fuel_code as code
            FROM partner_transactions c0_
            WHERE c0_.client_1c_id = :clientId
        ';

        $entityManager = $this->getQueryBuilder()
            ->getEntityManager();
        $conn = $entityManager
            ->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->execute(['clientId' => $partner->getClient1CId()]);

        $result = $stmt->fetchAll();

        $codes = [];
        foreach ($result as $item) {
            $codes[] = $item['code'];
        }

        return $codes;
    }

    public function getFuelCodes(): array
    {
        $sql = '
            SELECT 
                DISTINCT ON (c0_.fuel_code) c0_.fuel_code as code
            FROM partner_transactions c0_
        ';

        $entityManager = $this->getQueryBuilder()
            ->getEntityManager();
        $conn = $entityManager
            ->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll();

        $codes = [];
        foreach ($result as $item) {
            $codes[] = $item['code'];
        }

        return $codes;
    }

    public function calculatePartnerDebitSumByMonths(
        Partner $partner,
        Type $type,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): array {
        $sql = '
            SELECT
                date_trunc(\'month\', ct.post_date)::date AS date,
                sum(ct.debit) as sum
            FROM partner_transactions as ct
                WHERE ct.client_1c_id = :clientId
                    AND ct.post_date >= :startDate
                    AND ct.post_date <= :endDate
                    AND ct.write_off_type = :type
            GROUP BY date_trunc(\'month\', ct.post_date)::date;
        ';

        $entityManager = $this->getQueryBuilder()
            ->getEntityManager();
        $connection = $entityManager
            ->getConnection();

        $stmt = $connection->prepare($sql);
        $stmt->execute(
            [
                'clientId' => $partner->getClient1CId(),
                'type' => $type->getValue(),
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d'),
            ]
        );

        return $stmt->fetchAll();
    }
}
