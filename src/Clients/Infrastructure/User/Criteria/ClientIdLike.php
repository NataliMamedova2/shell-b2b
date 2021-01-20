<?php

namespace App\Clients\Infrastructure\User\Criteria;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\Company;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Filter\Like;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class ClientIdLike
{
    public function __invoke(Criteria $criteria, $value)
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

        $companyJoinAlias = 'company';
        if (!array_key_exists(Company::class, $joined)) {
            $query->innerJoin(Company::class, $companyJoinAlias, 'WITH', "$companyJoinAlias.id = $alias.company");
        } else {
            $companyJoinAlias = $joined[Company::class];
        }

        $clientJoinAlias = 'client';
        if (!array_key_exists(Client::class, $joined)) {
            $query->innerJoin(Client::class, $clientJoinAlias, 'WITH', "$clientJoinAlias.id = $companyJoinAlias.client");
        } else {
            $clientJoinAlias = $joined[Client::class];
        }

        $expr = Spec::andX(
            Spec::like(Spec::TRIM(Spec::LOWER('client1CId')), trim(mb_strtolower($value)), Like::CONTAINS, $clientJoinAlias)
        );

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
