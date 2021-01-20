<?php

namespace App\Clients\Infrastructure\FuelCard\Criteria;

use App\Clients\Domain\Driver\CarNumber;
use App\Clients\Domain\Driver\Driver;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Filter\Like;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class QueryStringCriteria
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $driverAlias = 'driver';
        $carNumberAlias = 'carNumber';

        $explodeValue = \explode(' ', $value);

        if (isset($explodeValue[0])) {
            $query->leftJoin(Driver::class, $driverAlias, 'WITH', "$driverAlias.id = $alias.driver");
            $query->leftJoin(CarNumber::class, $carNumberAlias, 'WITH', "$carNumberAlias.driver=$driverAlias.id");

            $expr = Spec::like('cardNumber', $explodeValue[0], Like::CONTAINS);
            $query->orWhere($expr->getFilter($query, $alias));
            $expr = Spec::like('carNumber', $explodeValue[0], Like::CONTAINS);
            $query->orWhere($expr->getFilter($query, $alias));
            $expr = Spec::like('number', $explodeValue[0], Like::CONTAINS);
            $query->orWhere($expr->getFilter($query, $carNumberAlias));
            $expr = Spec::like('name.lastName', \mb_convert_case($explodeValue[0], MB_CASE_TITLE, 'UTF-8'), Like::CONTAINS);
            $query->orWhere($expr->getFilter($query, $driverAlias));
            $expr = Spec::like('name.lastName', \mb_convert_case($explodeValue[0], MB_CASE_UPPER, 'UTF-8'), Like::CONTAINS);
            $query->orWhere($expr->getFilter($query, $driverAlias));
            $expr = Spec::like('name.lastName', \mb_convert_case($explodeValue[0], MB_CASE_LOWER, 'UTF-8'), Like::CONTAINS);
            $query->orWhere($expr->getFilter($query, $driverAlias));
        }
        if (isset($explodeValue[1])) {
            $splitValue = mb_str_split($explodeValue[1]);
            if (isset($splitValue[0])) {
                $expr = Spec::like('name.firstName', \mb_convert_case($splitValue[0], MB_CASE_TITLE, 'UTF-8'), Like::CONTAINS);
                $query->andWhere($expr->getFilter($query, $driverAlias));
                $expr = Spec::like('name.firstName', \mb_convert_case($splitValue[0], MB_CASE_LOWER, 'UTF-8'), Like::CONTAINS);
                $query->andWhere($expr->getFilter($query, $driverAlias));
            }
            if (isset($splitValue[1])) {
                $expr = Spec::like('name.middleName', \mb_convert_case($splitValue[1], MB_CASE_TITLE, 'UTF-8'), Like::CONTAINS);
                $query->andWhere($expr->getFilter($query, $driverAlias));
                $expr = Spec::like('name.middleName', \mb_convert_case($splitValue[1], MB_CASE_LOWER, 'UTF-8'), Like::CONTAINS);
                $query->andWhere($expr->getFilter($query, $driverAlias));
            }
        }
    }
}
