<?php

namespace App\Clients\Infrastructure\Driver\Criteria;

use Doctrine\ORM\QueryBuilder;
use Infrastructure\Interfaces\Criteria\Criteria;

final class OrderByName
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $orderField = 'fullNameOrder';
        $query->addSelect("CONCAT($alias.name.lastName, ' ', $alias.name.firstName, ' ', $alias.name.middleName) AS HIDDEN $orderField");

        $query->orderBy($orderField, $value);
    }
}
