<?php

namespace App\Api\Crud\DataTransformer;

use App\Api\Crud\Interfaces\DataTransformer;

final class SuccessDataTransformer implements DataTransformer
{
    public function transform($object)
    {
        return [
            'success' => true,
        ];
    }
}
