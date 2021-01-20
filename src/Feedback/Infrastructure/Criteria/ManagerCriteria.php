<?php

namespace App\Feedback\Infrastructure\Criteria;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\User\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class ManagerCriteria
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $relationAlias = 'u';
        $query->innerJoin(User::class, $relationAlias, Join::WITH, "$relationAlias.id = $alias.user");
        $companyRelationAlias = 'c';
        $query->innerJoin(Company::class, $companyRelationAlias, Join::WITH, "$relationAlias.company = $companyRelationAlias.id");
        $clientRelationAlias = 'cl';
        $query->innerJoin(Client::class, $clientRelationAlias, Join::WITH, "$companyRelationAlias.client = $clientRelationAlias.id");

        $expr = Spec::eq('manager1CId', $value, $clientRelationAlias);

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
