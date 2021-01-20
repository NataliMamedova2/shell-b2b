<?php

namespace App\Clients\Infrastructure\Driver\Criteria;

use Doctrine\ORM\QueryBuilder;
use Infrastructure\Interfaces\Criteria\Criteria;

final class OrderByStatus
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $orderField = 'statusOrder';
        $query->addSelect("CASE WHEN $alias.status = 0 THEN 'blocked' ELSE 'active' END AS HIDDEN $orderField");

        $query->orderBy($orderField, $value);
    }
}
