<?php

namespace App\Users\Infrastructure\Criteria;

use App\Clients\Domain\Client\Client;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class ManagerForClient
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();

        $alias = $query->getRootAliases()[0];

        $relationAlias = 'c';
        $query->innerJoin(Client::class, $relationAlias, Join::WITH, "$relationAlias.manager1CId = $alias.manager1CId");

        $activeExpr = Spec::andX(
            Spec::eq('id', $value, $relationAlias)
        );
        $query->andWhere($activeExpr->getFilter($query, $alias));

        $query->andWhere(
            "CONTAINS({$alias}.roles, :role) = true"
        );
        $query->setParameter('role', json_encode(['ROLE_MANAGER']));
    }
}
