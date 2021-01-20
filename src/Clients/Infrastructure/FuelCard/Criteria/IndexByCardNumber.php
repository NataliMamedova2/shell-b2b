<?php

namespace App\Clients\Infrastructure\FuelCard\Criteria;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class IndexByCardNumber
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        Spec::indexBy('cardNumber')
            ->modify($query, $alias);
    }
}
