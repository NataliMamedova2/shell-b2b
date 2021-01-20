<?php

namespace Infrastructure\Paginator;

use Doctrine\ORM\QueryBuilder;
use Infrastructure\Criteria\CriteriaFactory;
use Infrastructure\Interfaces\Paginator\Paginator;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

final class PagerfantaPaginator implements Paginator
{
    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var CriteriaFactory
     */
    private $criteriaFactory;

    public function __construct(CriteriaFactory $criteriaFactory, string $entityClass)
    {
        $this->criteriaFactory = $criteriaFactory;
        $this->entityClass = $entityClass;
    }

    public function paginate($criteria = null, ?array $order = null, ?int $page = 1, int $limit = 20): Pagerfanta
    {
        $criteria = $this->criteriaFactory->build($this->entityClass, $criteria);

        if (null !== $order) {
            $criteria = $this->criteriaFactory->buildOrder($criteria, $order);
        }

        $paginator = $this->getPaginator($criteria->getQuery());

        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return Pagerfanta
     */
    private function getPaginator($queryBuilder): Pagerfanta
    {
        return new Pagerfanta(new DoctrineORMAdapter($queryBuilder));
    }
}
