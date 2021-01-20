<?php

namespace App\Clients\Infrastructure\Driver\Criteria;

use App\Clients\Domain\Driver\CarNumber;
use App\Clients\Domain\Driver\Phone;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Infrastructure\Interfaces\Criteria\Criteria;

final class Search
{
    /**
     * @var Expr
     */
    private $expr;

    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $joinPhoneAlias = 'phone';
        $query->leftJoin(Phone::class, $joinPhoneAlias, 'WITH', "$joinPhoneAlias.driver = $alias.id");

        $joinCarNumberAlias = 'carNumber';
        $query->leftJoin(CarNumber::class, $joinCarNumberAlias, 'WITH', "$joinCarNumberAlias.driver = $alias.id");

        $valuesArr = array_diff(explode(' ', $value), ['']);

        $this->expr = $query->expr();
        $queryOrExpr = $this->expr->orX();

        if (isset($valuesArr[1])) {
            $likeExpr = $this->likeExpr("$alias.name.firstName", $valuesArr[1]);
            $queryOrExpr->add($likeExpr);

            $likeExpr = $this->likeExpr("$alias.name.middleName", $valuesArr[1]);
            $queryOrExpr->add($likeExpr);
        } elseif (isset($valuesArr[0])) {
            $likeExpr = $this->likeExpr("$alias.name.firstName", $valuesArr[0]);
            $queryOrExpr->add($likeExpr);

            $likeExpr = $this->likeExpr("$alias.name.middleName", $valuesArr[0]);
            $queryOrExpr->add($likeExpr);
        }

        if (isset($valuesArr[2])) {
            $likeExpr = $this->likeExpr("$alias.name.middleName", $valuesArr[2]);
            $queryOrExpr->add($likeExpr);
        }

        if (isset($valuesArr[0])) {
            $likeExpr = $this->likeExpr("$alias.name.lastName", $valuesArr[0]);
            $queryOrExpr->add($likeExpr);

            $likeExpr = $this->likeExpr("$alias.email", $valuesArr[0]);
            $queryOrExpr->add($likeExpr);

            $likeExpr = $this->likeExpr("$alias.note", $valuesArr[0]);
            $queryOrExpr->add($likeExpr);

            $likeExpr = $this->likeExpr("$joinPhoneAlias.number", $valuesArr[0]);
            $queryOrExpr->add($likeExpr);

            $likeExpr = $this->likeExpr("$joinCarNumberAlias.number", $valuesArr[0]);
            $queryOrExpr->add($likeExpr);
        }

        $query->andWhere($queryOrExpr);
    }

    private function likeExpr($field, $value)
    {
        return $this->expr->like(
            $this->expr->lower($field),
            $this->expr->lower($this->expr->literal("%$value%"))
        );
    }
}
