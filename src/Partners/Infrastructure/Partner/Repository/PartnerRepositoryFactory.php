<?php
namespace App\Partners\Infrastructure\Partner\Repository;

use App\Partners\Domain\Partner\Partner;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Criteria\CriteriaFactory;

final class PartnerRepositoryFactory
{
    public function __invoke(
        EntityManagerInterface $entityManager,
        CriteriaFactory $criteriaFactory
    ): PartnerRepository {
        return new PartnerRepository($entityManager, $criteriaFactory, Partner::class);
    }
}