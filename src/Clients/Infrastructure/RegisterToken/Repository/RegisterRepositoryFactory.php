<?php

namespace App\Clients\Infrastructure\RegisterToken\Repository;

use App\Clients\Domain\RegisterToken\Repository\RegisterRepository as DomainRepository;
use App\Clients\Domain\RegisterToken\Register;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Criteria\CriteriaFactory;

final class RegisterRepositoryFactory
{
    public function __invoke(
        EntityManagerInterface $entityManager,
        CriteriaFactory $criteriaFactory
    ): DomainRepository {
        return new RegisterRepository($entityManager, $criteriaFactory, Register::class);
    }
}
