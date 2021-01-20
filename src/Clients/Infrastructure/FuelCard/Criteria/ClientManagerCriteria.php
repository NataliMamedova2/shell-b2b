<?php

namespace App\Clients\Infrastructure\FuelCard\Criteria;

use App\Clients\Domain\Client\Client;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class ClientManagerCriteria
{
    public function __invoke(Criteria $criteria, string $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $joined = [];
        if (isset($query->getDQLPart('join')[$alias])) {
            /** @var Join $item */
            foreach ($query->getDQLPart('join')[$alias] as $item) {
                $joined[$item->getJoin()] = $item->getAlias();
            }
        }

        $joinAlias = 'client';
        if (!array_key_exists(Client::class, $joined)) {
            $query->innerJoin(Client::class, $joinAlias, 'WITH', "$joinAlias.client1CId = $alias.client1CId");
        } else {
            $joinAlias = $joined[Client::class];
        }

        $expr = Spec::eq('manager1CId', $value, $joinAlias);

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
