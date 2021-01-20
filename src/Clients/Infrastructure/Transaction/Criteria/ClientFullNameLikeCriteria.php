<?php

namespace App\Clients\Infrastructure\Transaction\Criteria;

use App\Clients\Domain\Client\Client;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Filter\Like;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class ClientFullNameLikeCriteria
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $joinAlias = 'client';
        $query->innerJoin(Client::class, $joinAlias, 'WITH', "$joinAlias.client1CId = $alias.client1CId");
        $expr = Spec::like(Spec::TRIM(Spec::LOWER('fullName')), trim(mb_strtolower($value)), Like::CONTAINS, $joinAlias);

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
