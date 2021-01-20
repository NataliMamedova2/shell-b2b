<?php

namespace App\Clients\Infrastructure\Transaction\Criteria;

use App\Clients\Domain\Fuel\Type\Type;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class SupplyTypeCriteria
{
    public function __invoke(Criteria $criteria, array $fuelTypes)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $joinAlias = 'type';
        $query->innerJoin(Type::class, $joinAlias, 'WITH', "$joinAlias.fuelCode = $alias.fuelCode");

        $expr = Spec::in('fuelType', $fuelTypes, $joinAlias);

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
