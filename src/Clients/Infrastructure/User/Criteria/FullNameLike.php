<?php

namespace App\Clients\Infrastructure\User\Criteria;

use Doctrine\ORM\QueryBuilder;
use Infrastructure\Interfaces\Criteria\Criteria;

final class FullNameLike
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $valuesArr = array_diff(explode(' ', $value), ['']);

        $expr = $query->expr();
        $queryOrExpr = $expr->orX();

        foreach ($valuesArr as $key => $value) {
            $likeExpr = $expr->like(
                $expr->lower("$alias.name.firstName"),
                $expr->lower($expr->literal("%$value%"))
            );
            $queryOrExpr->add($likeExpr);
            $likeExpr = $expr->like(
                $expr->lower("$alias.name.middleName"),
                $expr->lower($expr->literal("%$value%"))
            );
            $queryOrExpr->add($likeExpr);
            $likeExpr = $expr->like(
                $expr->lower("$alias.name.lastName"),
                $expr->lower($expr->literal("%$value%"))
            );
            $queryOrExpr->add($likeExpr);
        }

        $query->andWhere($queryOrExpr);
    }
}
