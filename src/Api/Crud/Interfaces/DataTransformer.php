<?php

namespace App\Api\Crud\Interfaces;

interface DataTransformer
{
    /**
     * @param object|array|mixed $object
     *
     * @return object|array|mixed
     */
    public function transform($object);
}
