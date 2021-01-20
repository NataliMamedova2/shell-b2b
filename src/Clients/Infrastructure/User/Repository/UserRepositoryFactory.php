<?php

namespace App\Clients\Infrastructure\User\Repository;

use App\Clients\Domain\User\Repository\UserRepository as DomainUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Criteria\CriteriaFactory;
use App\Clients\Domain\User\User;

final class UserRepositoryFactory
{
    public function __invoke(
        EntityManagerInterface $entityManager,
        CriteriaFactory $criteriaFactory
    ): DomainUserRepository {
        return new UserRepository($entityManager, $criteriaFactory, User::class);
    }
}
