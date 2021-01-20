<?php

namespace App\Users\Infrastructure\Repository;

use App\Users\Domain\User\User;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Repository\DoctrineRepository;

final class UserRepository extends DoctrineRepository implements \App\Users\Domain\User\Repository\UserRepository
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

//
//    public function findEmailsByRole(string $role)
//    {
//        $queryBuilder = $this->getQueryBuilder();
//
//        $alias = $queryBuilder->getRootAliases()[0];
//        $queryBuilder->select(["{$alias}.email"]);
//        $queryBuilder->where(
//            "CONTAINS({$alias}.roles, :role) = true"
//        );
//
//        $queryBuilder->setParameter('role', json_encode([$role]));
//
//        return array_map("current", $queryBuilder->getQuery()->getScalarResult());
//    }
}
