<?php

namespace App\Clients\Infrastructure\Client\Criteria;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class IndexByClient1CId
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        Spec::indexBy('client1CId')
            ->modify($query, $alias);
    }
}
