<?php

namespace App\Clients\Infrastructure\User\Repository;

use App\Clients\Domain\Company\Company;
use App\Clients\Infrastructure\User\Criteria\Role;
use Infrastructure\Repository\DoctrineRepository;
use App\Clients\Domain\User\Repository\UserRepository as DomainUserRepository;
use App\Clients\Domain\User\User;
use Happyr\DoctrineSpecification\Spec;
use \App\Clients\Domain\User\ValueObject\Role as RoleValueObject;

final class UserRepository extends DoctrineRepository implements DomainUserRepository
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

    public function findByToken(string $token): ?User
    {
        $queryBuilder = $this->getQueryBuilder();
        $alias = $queryBuilder->getAllAliases()[0];

        $spec = Spec::eq('restorePassToken.token', $token);
        $queryBuilder->andWhere($spec->getFilter($queryBuilder, $alias));

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function getAdminInCompanyCount()
    {
        $queryBuilder = $this->getQueryBuilder();
        $alias = $queryBuilder->getAllAliases()[0];

        $joinAlias = 'c';
        $queryBuilder->innerJoin(Company::class, $joinAlias, 'WITH', "$joinAlias.id = $alias.company");

        $queryBuilder
            ->select(["$joinAlias.id, COUNT($alias.id) as count"])
            ->andWhere(
                "CONTAINS({$alias}.roles, :role) = true"
            )
            ->groupBy("$joinAlias.id");

        $queryBuilder->setParameter('role', json_encode([RoleValueObject::admin()->getValue()]));

        return $queryBuilder->getQuery()->getResult();
    }
}
