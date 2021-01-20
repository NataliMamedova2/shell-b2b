<?php

namespace App\Clients\Infrastructure\Client\Criteria;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Filter\Like;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class ManagerIdLike
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $expr = Spec::andX(
            Spec::like(Spec::TRIM(Spec::LOWER('manager1CId')), trim(mb_strtolower($value)), Like::CONTAINS)
        );

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
