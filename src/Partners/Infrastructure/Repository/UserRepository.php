<?php

namespace App\Partners\Infrastructure\Repository;

use App\Partners\Domain\User\Repository\UserRepository as BaseUserRepository;
use App\Partners\Domain\User\User;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Repository\DoctrineRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

final class UserRepository extends DoctrineRepository implements BaseUserRepository, UserLoaderInterface
{
    public function findByUsernameOrEmail(string $username, string $email): ?User
    {
        $queryBuilder = $this->getQueryBuilder();
        $alias = $queryBuilder->getAllAliases()[0];

        $spec = Spec::orX(
            Spec::eq('username', $username),
            Spec::eq('email', $email)
        );
        $queryBuilder->andWhere($spec->getFilter($queryBuilder, $alias));

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function loadUserByUsername(string $username)
    {
        return $this->findByUsernameOrEmail($username, '');
    }
}
