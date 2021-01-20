<?php

namespace App\Api\Crud\DataTransformer;

use App\Api\Crud\Interfaces\DataTransformer;
use App\Api\Resource\Model;

class ListDataTransformer implements DataTransformer
{
    /**
     * @var Model
     */
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public function transform($array)
    {
        $collection = [];

        foreach ($array as $item) {
            $model = clone $this->model;
            $collection[] = $model->prepare($item);
        }

        return $collection;
    }
}
