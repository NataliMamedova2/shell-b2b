<?php
namespace App\Partners\Infrastructure\Repository;

use App\Partners\Domain\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Criteria\CriteriaFactory;

final class UserRepositoryFactory
{
    public function __invoke(
        EntityManagerInterface $entityManager,
        CriteriaFactory $criteriaFactory
    ): \App\Partners\Domain\User\Repository\UserRepository {
        return new UserRepository($entityManager, $criteriaFactory, User::class);
    }
}