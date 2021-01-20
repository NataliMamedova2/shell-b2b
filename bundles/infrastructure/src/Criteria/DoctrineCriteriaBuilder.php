<?php

declare(strict_types=1);

namespace Infrastructure\Criteria;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Spec;
use Infrastructure\Interfaces\Criteria\Criteria;

final class DoctrineCriteriaBuilder implements Criteria
{
    /**
     * @var QueryBuilder
     */
    private $select;

    /**
     * @var string
     */
    private $alias = 'e';

    /**
     * @var array
     */
    private $relations = [];

    public function __construct($entityName, EntityManagerInterface $entityManager, $alias = 'e')
    {
        $this->alias = $alias;
        $this->select = $entityManager
            ->createQueryBuilder()
            ->select([$alias])
            ->from($entityName, $alias);
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->select;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->select->getQuery();
    }

    /**
     * @param string                $attribute
     * @param bool|int|float|string $value
     *
     * @return $this
     */
    public function equalTo($attribute, $value)
    {
        $field = $this->getField($attribute);

        $this->select->andWhere(
            Spec::eq($field['attribute'], $value)
                ->getFilter($this->getQueryBuilder(), $field['alias'])
        );

        return $this;
    }

    /**
     * @param string                $attribute
     * @param bool|int|float|string $value
     *
     * @return $this
     */
    public function notEqualTo($attribute, $value)
    {
        $field = $this->getField($attribute);

        $this->select->andWhere(
            Spec::neq($field['attribute'], $value)
                ->getFilter($this->getQueryBuilder(), $field['alias'])
        );

        return $this;
    }

    /**
     * @param string    $attribute
     * @param int|float $value
     *
     * @return $this
     */
    public function lessThan($attribute, $value)
    {
        $field = $this->getField($attribute);

        $this->select->andWhere(
            Spec::lt($field['attribute'], $value)
                ->getFilter($this->getQueryBuilder(), $field['alias'])
        );

        return $this;
    }

    /**
     * @param string    $attribute
     * @param int|float $value
     *
     * @return $this
     */
    public function greaterThan($attribute, $value)
    {
        $field = $this->getField($attribute);

        $this->select->andWhere(
            Spec::gt($field['attribute'], $value)
                ->getFilter($this->getQueryBuilder(), $field['alias'])
        );

        return $this;
    }

    /**
     * @param string    $attribute
     * @param int|float $value
     *
     * @return $this
     */
    public function greaterThanOrEqualTo($attribute, $value)
    {
        $field = $this->getField($attribute);

        $this->select->andWhere(
            Spec::gte($field['attribute'], $value)
                ->getFilter($this->getQueryBuilder(), $field['alias'])
        );

        return $this;
    }

    /**
     * @param string    $attribute
     * @param int|float $value
     *
     * @return $this
     */
    public function lessThanOrEqualTo($attribute, $value)
    {
        $field = $this->getField($attribute);

        $this->select->andWhere(
            Spec::lte($field['attribute'], $value)
                ->getFilter($this->getQueryBuilder(), $field['alias'])
        );

        return $this;
    }

    /**
     * @param string    $attribute
     * @param int|float $value
     *
     * @return $this
     */
    public function like($attribute, $value)
    {
        $field = $this->getField($attribute);

        $this->select->andWhere(
            Spec::like($field['attribute'], $value)
                ->getFilter($this->getQueryBuilder(), $field['alias'])
        );

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function isNull($attribute)
    {
        $field = $this->getField($attribute);

        $this->select->andWhere(
            Spec::isNull($field['attribute'])
                ->getFilter($this->getQueryBuilder(), $field['alias'])
        );

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function isNotNull($attribute)
    {
        $field = $this->getField($attribute);

        $this->select->andWhere(
            Spec::isNotNull($field['attribute'])
                ->getFilter($this->getQueryBuilder(), $field['alias'])
        );

        return $this;
    }

    /**
     * @param string $attribute
     * @param array  $values
     *
     * @return $this
     */
    public function in($attribute, array $values)
    {
        $field = $this->getField($attribute);

        $this->select->andWhere(
            Spec::in($field['attribute'], $values)
                ->getFilter($this->getQueryBuilder(), $field['alias'])
        );

        return $this;
    }

    /**
     * @param string $attribute
     * @param array  $values
     *
     * @return $this
     */
    public function notIn($attribute, array $values)
    {
        $field = $this->getField($attribute);

        $this->select->andWhere(
            Spec::notIn($field['attribute'], $values)
                ->getFilter($this->getQueryBuilder(), $field['alias'])
        );

        return $this;
    }

    /**
     * @param array $orders[]
     *
     * @return $this
     */
    public function order(array $orders)
    {
        foreach ($orders as $attribute => $order) {
            $field = $this->getField($attribute);
            Spec::orderBy($field['attribute'], $order, $field['alias'])
                ->modify($this->getQueryBuilder(), $this->alias);
        }

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->select->setMaxResults($limit);

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->select->setFirstResult($offset);

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return array
     */
    public function getField($attribute): array
    {
        $alias = $this->alias;
        if (false !== strpos($attribute, '.')) {
            $expressionArray = explode('.', $attribute);

            if (2 === count($expressionArray)) {
                $relation = $expressionArray[0];
                $attribute = $expressionArray[1];

                $alias = $relation;

                if (!in_array($relation, $this->relations)) {
                    array_push($this->relations, $relation);
                }

                $joinParts = $this->getQueryBuilder()->getDQLPart('join');

                $joins = [];
                if (isset($joinParts[$this->alias])) {
                    $newJoin = $this->alias.'.'.$relation;
                    $joins = array_filter($joinParts[$this->alias], static function ($value) use ($newJoin) {
                        return $value->getJoin() === $newJoin;
                    });
                }
                if (!$joins) {
                    Spec::join($relation, $alias, $this->alias)
                        ->modify($this->getQueryBuilder(), $alias);
                } else {
                    $alias = $joins[0]->getAlias();
                }
            }
        }

        return [
            'attribute' => $attribute,
            'alias' => $alias,
        ];
    }
}
