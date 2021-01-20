<?php

namespace App\Clients\Infrastructure\Fuel\Criteria;

use App\Clients\Domain\Fuel\Type\Type;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class IndexByFuelCode
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        Spec::indexBy('fuelCode')
            ->modify($query, $alias);
    }
}
