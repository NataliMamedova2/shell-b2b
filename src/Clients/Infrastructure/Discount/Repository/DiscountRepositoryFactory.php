<?php

namespace App\Clients\Infrastructure\Discount\Repository;

use App\Clients\Domain\Discount\Discount;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Criteria\CriteriaFactory;

final class DiscountRepositoryFactory
{
    public function __invoke(
        EntityManagerInterface $entityManager,
        CriteriaFactory $criteriaFactory
    ): DiscountRepository {
        return new DiscountRepository($entityManager, $criteriaFactory, Discount::class);
    }
}
