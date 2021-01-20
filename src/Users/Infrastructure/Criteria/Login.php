<?php

namespace App\Users\Infrastructure\Criteria;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class Login
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();

        $alias = $query->getRootAliases()[0];
        $activeExpr = Spec::andX(
            Spec::orX(
                Spec::eq('email', $value),
                Spec::eq('username', $value)
            )
        );
        $query->andWhere($activeExpr->getFilter($query, $alias));
    }
}
