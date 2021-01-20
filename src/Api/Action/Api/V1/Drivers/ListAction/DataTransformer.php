<?php

namespace App\Api\Action\Api\V1\Drivers\ListAction;

use App\Api\Resource\Driver;
use Pagerfanta\Pagerfanta;

final class DataTransformer implements \App\Api\Crud\Interfaces\DataTransformer
{
    /**
     * @param Pagerfanta $paginator
     *
     * @return array
     */
    public function transform($paginator)
    {
        if (!$paginator instanceof Pagerfanta) {
            throw new \InvalidArgumentException();
        }

        $collection = [];
        foreach ($paginator->getCurrentPageResults() as $document) {
            $model = new Driver();
            $collection[] = $model->prepare($document);
        }

        return [
            'meta' => [
                'pagination' => [
                    'totalCount' => $paginator->getNbPages(),
                    'currentPage' => $paginator->getCurrentPage(),
                ],
            ],
            'data' => $collection,
        ];
    }
}
