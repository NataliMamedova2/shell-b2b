<?php

namespace App\Users\Infrastructure\Repository;

use App\Users\Domain\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Criteria\CriteriaFactory;

final class UserRepositoryFactory
{
    public function __invoke(
        EntityManagerInterface $entityManager,
        CriteriaFactory $criteriaFactory
    ): \App\Users\Domain\User\Repository\UserRepository {
        return new UserRepository($entityManager, $criteriaFactory, User::class);
    }
}
