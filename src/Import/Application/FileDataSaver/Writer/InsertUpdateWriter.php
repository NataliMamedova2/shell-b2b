<?php

namespace App\Import\Application\FileDataSaver\Writer;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;

abstract class InsertUpdateWriter extends DoctrineEntityWriter
{
    public function handleArray(\ArrayIterator $arrayIterator)
    {
        $this->update($arrayIterator);

        $this->insert($arrayIterator);
    }

    private function update(\ArrayIterator $arrayIterator): void
    {
        $query = $this->buildSelectQuery($this->entityManager, $arrayIterator);

        if ($query instanceof AbstractQuery) {
            $iterable = SimpleBatchIteratorAggregate::fromQuery($query, $this->batchSize);
            foreach ($iterable as $entity) {
                $uniqueKey = $this->getUniqueKeyFromEntity($entity[0]);

                if ($arrayIterator->offsetExists($uniqueKey)) {
                    $record = $arrayIterator->offsetGet($uniqueKey);
                    $arrayIterator->offsetUnset($uniqueKey);

                    try {
                        $this->updateEntity($entity[0], $record);
                    } catch (\Exception $e) {
                        $this->addException($e);
                    }
                }
            }
        }
    }

    /**
     * Return primary key from entity for matching data in db and record.
     * Need only if buildSelectQuery return AbstractQuery.
     *
     * @param object $entity
     *
     * @return string|null
     */
    abstract public function getUniqueKeyFromEntity(object $entity): ?string;

    /**
     * Update existing entity found by buildSelectQuery
     * Need only if buildSelectQuery return AbstractQuery.
     *
     * @param object $entity
     * @param array  $record
     */
    abstract public function updateEntity(object $entity, array $record): void;

    /**
     * If need update already exists entity build Query SELECT for extract all of them.
     *
     * @param EntityManagerInterface $entityManager
     * @param \ArrayIterator         $items
     *
     * @return AbstractQuery|null
     */
    abstract public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery;
}
