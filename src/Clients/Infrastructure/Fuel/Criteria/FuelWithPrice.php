<?php

namespace App\Clients\Infrastructure\Fuel\Criteria;

use App\Clients\Domain\Fuel\Price\Price;
use Doctrine\ORM\QueryBuilder;
use Infrastructure\Interfaces\Criteria\Criteria;

final class FuelWithPrice
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $joinAlias = 'price';
        $query->innerJoin(Price::class, $joinAlias, 'WITH', "$joinAlias.fuelCode = $alias.fuelCode");
    }
}
