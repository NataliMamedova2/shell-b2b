<?php

namespace App\Clients\Infrastructure\FuelCard\Criteria;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class IsBlocked
{
    public function __invoke(Criteria $criteria, $cardNumber)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $expr = Spec::andX(
            Spec::eq('cardNumber', $cardNumber),
            Spec::orX(
                Spec::eq('status', 0),
                Spec::eq('status', 1)
            )
        );

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
