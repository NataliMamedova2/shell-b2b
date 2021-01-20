<?php

declare(strict_types=1);

namespace CrudBundle\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Criteria\CriteriaFactory;
use Infrastructure\Interfaces\Repository\Repository;
use Infrastructure\Repository\DoctrineRepository;

final class DoctrineRepositoryFactory
{
    public function __invoke(
        EntityManagerInterface $entityManager,
        CriteriaFactory $criteriaFactory,
        string $entityClass
    ): Repository {
        return new DoctrineRepository($entityManager, $criteriaFactory, $entityClass);
    }
}
