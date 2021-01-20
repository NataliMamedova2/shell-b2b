<?php

namespace App\Clients\Infrastructure\Fuel\Criteria;

use App\Clients\Domain\Fuel\Type\Type;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Infrastructure\Interfaces\Criteria\Criteria;

final class FuelNameOrder
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $joined = [];
        if (isset($query->getDQLPart('join')[$alias])) {
            /** @var Join $item */
            foreach ($query->getDQLPart('join')[$alias] as $item) {
                $joined[$item->getJoin()] = $item->getAlias();
            }
        }

        $joinAlias = 'type';
        if (!array_key_exists(Type::class, $joined)) {
            $query->innerJoin(Type::class, $joinAlias, 'WITH', "$joinAlias.fuelCode = $alias.fuelCode");
        } else {
            $joinAlias = $joined[Type::class];
        }

        $query->orderBy("$joinAlias.fuelName", $value);
    }
}
