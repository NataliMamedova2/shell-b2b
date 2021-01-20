<?php

namespace App\Export\Infrastructure\Criteria;

use App\Export\Domain\Export\ValueObject\Type;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class Type1C
{
    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();

        $alias = $query->getRootAliases()[0];
        $activeExpr = Spec::andX(
            Spec::eq('type', Type::type1C()->getValue())
        );

        $query->andWhere($activeExpr->getFilter($query, $alias));
    }
}
