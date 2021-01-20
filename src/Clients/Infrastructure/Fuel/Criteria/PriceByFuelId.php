<?php

namespace App\Clients\Infrastructure\Fuel\Criteria;

use App\Clients\Domain\Fuel\Type\Type;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class PriceByFuelId
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $joinAlias = 'type';
        $query->innerJoin(Type::class, $joinAlias, 'WITH', "$joinAlias.fuelCode = $alias.fuelCode");

        $expr = Spec::andX(Spec::eq('id', $value, $joinAlias));

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
