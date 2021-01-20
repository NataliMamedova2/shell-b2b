<?php

namespace App\Api\Infractructure\Criteria;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class MethodCriteria
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $expr = Spec::andX(
            Spec::eq(Spec::LOWER('request.method'), mb_strtolower($value))
        );

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
