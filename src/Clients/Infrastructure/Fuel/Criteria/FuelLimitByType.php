<?php

namespace App\Clients\Infrastructure\Fuel\Criteria;

use App\Clients\Domain\Fuel\Type\Type;
use Doctrine\ORM\QueryBuilder;
use Infrastructure\Interfaces\Criteria\Criteria;

final class FuelLimitByType
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $joinAlias = 'type';
        $query->innerJoin(Type::class, $joinAlias, 'WITH', "$joinAlias.fuelCode = $alias.fuelCode");

        $fuelType = $query->expr()->eq("$joinAlias.fuelType", $value);
        $query->andWhere($fuelType);
    }
}
