<?php

namespace App\Clients\Infrastructure\User\Criteria;

use App\Clients\Domain\Company\Company;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Filter\Like;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class CompanyLike
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

        $joinAlias = 'company';
        if (!array_key_exists(Company::class, $joined)) {
            $query->innerJoin(Company::class, $joinAlias, 'WITH', "$joinAlias.id = $alias.company");
        } else {
            $joinAlias = $joined[Company::class];
        }

        $expr = Spec::andX(
            Spec::like(Spec::TRIM(Spec::LOWER('name')), trim(mb_strtolower($value)), Like::CONTAINS, $joinAlias)
        );

        $query->andWhere($expr->getFilter($query, $alias));
    }
}
