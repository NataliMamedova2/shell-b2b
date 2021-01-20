<?php

namespace App\Clients\Infrastructure\ClientInfo\Criteria;

use App\Clients\Domain\Client\Client;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class ByClient
{
    public function __invoke(Criteria $criteria, Client $client)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $expr = Spec::andX(
            Spec::eq(Spec::field('clientPcId'), $client->getClientPcId())
        );

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
