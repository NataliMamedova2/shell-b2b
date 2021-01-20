<?php

namespace App\Clients\Infrastructure\Client\Criteria;

use App\Clients\Domain\Card\Card;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Filter\Like;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class CardNumberCriteria
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $joinAlias = 'card';
        $query->innerJoin(Card::class, $joinAlias, 'WITH', "$joinAlias.client1CId = $alias.client1CId");

        $expr = Spec::andX(
            Spec::like('cardNumber', $value, Like::CONTAINS, $joinAlias)
        );
        $query->andWhere($expr->getFilter($query, $alias));
    }
}
