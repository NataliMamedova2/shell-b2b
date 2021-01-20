<?php

namespace App\Import\Application\FileDataSaver\Writer;

abstract class InsertDeleteWriter extends DoctrineEntityWriter
{
    public function handleArray(\ArrayIterator $arrayIterator)
    {
        $this->delete($arrayIterator);
        $this->insert($arrayIterator);
    }

    abstract public function delete(\ArrayIterator $arrayIterator): void;
}
