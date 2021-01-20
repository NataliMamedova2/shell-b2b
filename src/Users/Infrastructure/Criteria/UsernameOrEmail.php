<?php

namespace App\Users\Infrastructure\Criteria;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class UsernameOrEmail
{
    public function __invoke(Criteria $criteria, $value)
    {
        if (!isset($value['email'])) {
            throw new \InvalidArgumentException(sprintf('Key "%s" required in criteria "%s"', 'email', get_class($this)));
        }
        if (!isset($value['username'])) {
            throw new \InvalidArgumentException(sprintf('Key "%s" required in criteria "%s"', 'username', get_class($this)));
        }

        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();

        $alias = $query->getRootAliases()[0];
        $activeExpr = Spec::andX(
            Spec::orX(
                Spec::eq('email', $value['email']),
                Spec::eq('username', $value['username'])
            )
        );
        $query->andWhere($activeExpr->getFilter($query, $alias));
    }
}
