<?php

namespace App\Clients\Infrastructure\ClientInfo\Criteria;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class IndexByClientPсId
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        Spec::indexBy('clientPcId')
            ->modify($query, $alias);
    }
}
