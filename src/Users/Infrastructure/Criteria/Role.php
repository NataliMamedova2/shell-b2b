<?php

namespace App\Users\Infrastructure\Criteria;

use Doctrine\ORM\QueryBuilder;
use Infrastructure\Interfaces\Criteria\Criteria;

final class Role
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $query->andWhere(
            "CONTAINS({$alias}.roles, :role) = true"
        );
        $query->setParameter('role', json_encode([$value]));
    }
}
