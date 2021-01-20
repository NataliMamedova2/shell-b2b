<?php

namespace App\Api\Crud\DataTransformer;

use App\Api\Crud\Interfaces\DataTransformer;
use Pagerfanta\Pagerfanta;

class PaginationDataTransformer implements DataTransformer
{
    /**
     * @var Pagerfanta
     */
    protected $paginator;

    /**
     * @param array|mixed $object
     *
     * @return array
     */
    public function transform($object)
    {
        $this->paginator = $object;

        return [
            'meta' => $this->getMeta(),
            'data' => $this->paginator,
        ];
    }

    protected function getMeta(): array
    {
        return [
            'pagination' => [
                'totalCount' => $this->paginator->getNbPages(),
                'currentPage' => $this->paginator->getCurrentPage(),
            ],
        ];
    }
}
