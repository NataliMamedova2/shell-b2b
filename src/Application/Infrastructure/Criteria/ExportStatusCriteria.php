<?php

namespace App\Application\Infrastructure\Criteria;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class ExportStatusCriteria
{
    public function __invoke(Criteria $criteria, $value)
    {
        if (false === is_integer($value) && false === is_string($value) && false === is_array($value)) {
            throw new \InvalidArgumentException('value can be string or array');
        }

        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        $field = Spec::field('exportStatus.exportStatus', $alias);

        $expr = Spec::eq($field, $value);
        if (is_array($value)) {
            $expr = Spec::in($field, $value);
        }
        $query->andWhere($expr->getFilter($query, $alias));
    }
}
