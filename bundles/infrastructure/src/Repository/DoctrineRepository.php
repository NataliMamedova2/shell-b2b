<?php

namespace Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Infrastructure\Exception\InvalidArgumentException;
use Infrastructure\Interfaces\Criteria\CriteriaFactory;
use Infrastructure\Interfaces\Repository\Repository;

class DoctrineRepository implements Repository
{
    private $entityManager;

    protected $entityClass;

    /**
     * @var CriteriaFactory
     */
    protected $criteriaFactory;

    public function __construct(EntityManagerInterface $entityManager, CriteriaFactory $criteriaFactory, string $entityClass)
    {
        $this->entityManager = $entityManager;
        $this->criteriaFactory = $criteriaFactory;
        $this->entityClass = $entityClass;
    }

    public function add(object $entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function remove(object $entity): void
    {
        $this->entityManager->remove($entity);
    }

    /**
     * @param string $id
     *
     * @return object|null
     */
    public function findById(string $id)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Argument "id" is required');
        }
        $criteria = [
            'id_equalTo' => $id,
        ];

        return $this->find($criteria);
    }

    /**
     * @param array $criteria
     * @param array|null $order
     *
     * @return object|null
     */
    public function find($criteria, array $order = null)
    {
        $criteria = $this->criteriaFactory->build($this->entityClass, $criteria);

        if (null !== $order) {
            $criteria = $this->criteriaFactory->buildOrder($criteria, $order);
        }
        $criteria->limit(1);

        return $criteria->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array      $criteria
     * @param array|null $order
     * @param null       $limit
     * @param null       $offset
     *
     * @return array
     */
    public function findMany(array $criteria = null, ?array $order = null, $limit = null, $offset = null): array
    {
        $criteria = $this->criteriaFactory->build($this->entityClass, $criteria);

        if (null !== $order) {
            $criteria = $this->criteriaFactory->buildOrder($criteria, $order);
        }

        if (null !== $limit) {
            $criteria->limit((int) $limit);
        }
        if (null !== $offset) {
            $criteria->offset((int) $offset);
        }

        return $criteria->getQuery()->getResult();
    }

    /**
     * @param mixed $criteria
     *
     * @return int
     * @throws NonUniqueResultException
     */
    public function count(array $criteria = []): int
    {
        $criteria = $this->criteriaFactory->build($this->entityClass, $criteria);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $criteria->getQueryBuilder();
        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->select(["COUNT({$alias}.id)"]);

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select(['e'])
            ->from($this->entityClass, 'e');
    }
}
