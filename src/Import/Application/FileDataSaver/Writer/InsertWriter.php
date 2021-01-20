<?php

namespace App\Import\Application\FileDataSaver\Writer;

abstract class InsertWriter extends DoctrineEntityWriter
{
    public function handleArray(\ArrayIterator $arrayIterator)
    {
        $this->insert($arrayIterator);
    }
}
