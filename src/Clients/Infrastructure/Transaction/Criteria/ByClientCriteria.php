<?php

namespace App\Clients\Infrastructure\Transaction\Criteria;

use App\Clients\Domain\Client\Client;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class ByClientCriteria
{
    public function __invoke(Criteria $criteria, Client $client)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $expr = Spec::orX(
            Spec::eq('client1CId', $client->getClient1CId()),
            Spec::eq('fcCbrId', $client->getClientPcId())
        );

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
