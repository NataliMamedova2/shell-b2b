<?php

namespace App\Clients\Infrastructure\Invoice\Criteria;

use Doctrine\ORM\QueryBuilder;
use Infrastructure\Interfaces\Criteria\Criteria;

final class DateOrder
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $query->orderBy("$alias.date.creationDate", $value);
    }
}
