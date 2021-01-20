<?php

declare(strict_types=1);

namespace Infrastructure\Criteria;

use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Criteria\Criteria as CriteriaInterface;
use Infrastructure\Interfaces\Criteria\CriteriaFactory as CriteriaFactoryInterface;
use RuntimeException;

final class CriteriaFactory implements CriteriaFactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * CriteriaFactory constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $entityName
     * @param array  $filter
     * @param string $alias
     *
     * @return CriteriaInterface
     */
    public function build($entityName, array $filter = [], $alias = 'e'): CriteriaInterface
    {
        if (!is_string($entityName)) {
            throw new RuntimeException(sprintf('Entity mame must be string, %s given', gettype($entityName)));
        }

        $criteria = new DoctrineCriteriaBuilder($entityName, $this->entityManager, $alias);

        if (empty($filter)) {
            return $criteria;
        }

        $this->applyFilter($criteria, $filter);

        return $criteria;
    }

    public function buildOrder(CriteriaInterface $criteria, array $order = []): CriteriaInterface
    {
        foreach ($order as $expressionString => $value) {
            if (class_exists($expressionString)) {
                $customCriteriaInstance = $this->buildCustomCriteria($expressionString);
                $customCriteriaInstance($criteria, $value);

                continue;
            }
            $criteria->order([$expressionString => $value]);
        }

        return $criteria;
    }

    /**
     * @param CriteriaInterface $criteria
     * @param array             $filter
     *
     * @return CriteriaInterface
     */
    private function applyFilter(CriteriaInterface $criteria, array $filter)
    {
        foreach ($filter as $expressionString => $value) {
            if (false === is_string($expressionString)) {
                throw new RuntimeException(sprintf('Bad criteria "%s". Format: key > value', $expressionString));
            }

            $expressionArray = [];
            if (false !== strpos($expressionString, '_')) {
                $expressionArray = explode('_', $expressionString);
            }

            if (count($expressionArray) > 2) {
                continue;
            }

            if (2 == count($expressionArray)) {
                list($attribute, $method) = $expressionArray;

                if (in_array($method, ['isNull', 'isNotNull']) && $value) {
                    $criteria->{$method}($attribute);
                    continue;
                }

                if (!method_exists($criteria, $method)) {
                    throw new RuntimeException(sprintf('Predicate %s does not exists', $method));
                }

                if (null !== $value) {
                    call_user_func([$criteria, $method], $attribute, $value);
                }
            }

            if (empty($expressionArray)) {
                $customCriteriaInstance = $this->buildCustomCriteria($expressionString);
                $customCriteriaInstance($criteria, $value);
            }
        }

        return $criteria;
    }

    /**
     * @param string $criteriaName
     *
     * @return mixed
     */
    public function buildCustomCriteria(string $criteriaName)
    {
        $customCriteriaClass = $criteriaName;

        if (!class_exists($customCriteriaClass)) {
            throw new RuntimeException(sprintf('Wrong criteria. Criteria Class "%s" does not exists.', $customCriteriaClass));
        }

        $customCriteria = new $customCriteriaClass();

        if (!is_callable($customCriteria)) {
            throw new RuntimeException(sprintf('Wrong criteria. Object of type %s is not callable.', $customCriteriaClass));
        }

        return $customCriteria;
    }
}
