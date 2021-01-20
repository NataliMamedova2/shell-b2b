<?php

namespace App\Clients\Infrastructure\ShellInformation\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Criteria\CriteriaFactory;
use App\Clients\Domain\ShellInformation\ShellInformation;

final class RepositoryFactory
{
    public function __invoke(
        EntityManagerInterface $entityManager,
        CriteriaFactory $criteriaFactory
    ): ShellInfoRepository {
        return new ShellInfoRepository($entityManager, $criteriaFactory, ShellInformation::class);
    }
}
