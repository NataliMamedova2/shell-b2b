<?php

declare(strict_types=1);

namespace Infrastructure\Interfaces\Repository;

interface Repository
{
    /**
     * @param object $entity
     */
    public function add(object $entity): void;

    /**
     * @param object $entity
     */
    public function remove(object $entity): void;

    /**
     * @param array      $criteria
     * @param array|null $order
     *
     * @return object|null
     */
    public function find(array $criteria, array $order = null);

    /**
     * @param array|null $criteria
     * @param array|null $order
     * @param null       $limit
     * @param null       $offset
     *
     * @return array
     */
    public function findMany(array $criteria = null, array $order = null, $limit = null, $offset = null): array;

    /**
     * @param string $id
     *
     * @return object|null
     */
    public function findById(string $id);

    /**
     * @param mixed $criteria
     *
     * @return int
     */
    public function count(array $criteria = []): int;
}
