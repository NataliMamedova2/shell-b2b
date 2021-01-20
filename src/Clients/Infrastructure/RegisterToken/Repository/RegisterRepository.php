<?php

namespace App\Clients\Infrastructure\RegisterToken\Repository;

use App\Clients\Domain\RegisterToken\Repository\RegisterRepository as DomainRepository;
use Infrastructure\Repository\DoctrineRepository;
use App\Clients\Domain\RegisterToken\Register;
use Happyr\DoctrineSpecification\Spec;

final class RegisterRepository extends DoctrineRepository implements DomainRepository
{
    public function findByToken(string $token): ?Register
    {
        $queryBuilder = $this->getQueryBuilder();
        $alias = $queryBuilder->getAllAliases()[0];

        $spec = Spec::eq('token.token', $token);
        $queryBuilder->andWhere($spec->getFilter($queryBuilder, $alias));

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
