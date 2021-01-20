<?php

declare(strict_types=1);

namespace Infrastructure\Interfaces\Criteria;

interface Criteria
{
    /**
     * @return mixed
     */
    public function getQueryBuilder();

    /**
     * @return mixed
     */
    public function getQuery();

    /**
     * @param string                $attribute
     * @param bool|int|float|string $value
     *
     * @return $this
     */
    public function equalTo($attribute, $value);

    /**
     * @param string                $attribute
     * @param bool|int|float|string $value
     *
     * @return $this
     */
    public function notEqualTo($attribute, $value);

    /**
     * @param string    $attribute
     * @param int|float $value
     *
     * @return $this
     */
    public function lessThan($attribute, $value);

    /**
     * @param string    $attribute
     * @param int|float $value
     *
     * @return $this
     */
    public function greaterThan($attribute, $value);

    /**
     * @param string    $attribute
     * @param int|float $value
     *
     * @return $this
     */
    public function greaterThanOrEqualTo($attribute, $value);

    /**
     * @param string    $attribute
     * @param int|float $value
     *
     * @return $this
     */
    public function lessThanOrEqualTo($attribute, $value);

    /**
     * @param string    $attribute
     * @param int|float $value
     *
     * @return $this
     */
    public function like($attribute, $value);

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function isNull($attribute);

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function isNotNull($attribute);

    /**
     * @param string $attribute
     * @param array  $values
     *
     * @return $this
     */
    public function in($attribute, array $values);

    /**
     * @param string $attribute
     * @param array  $values
     *
     * @return $this
     */
    public function notIn($attribute, array $values);

    /**
     * @param array $attribute
     *
     * @return $this
     */
    public function order(array $attribute);

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function limit(int $limit);

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function offset(int $offset);

    /**
     * @param string $attribute
     *
     * @return mixed
     */
    public function getField($attribute);
}
