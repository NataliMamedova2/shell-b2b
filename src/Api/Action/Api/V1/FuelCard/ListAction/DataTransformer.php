<?php

namespace App\Api\Action\Api\V1\FuelCard\ListAction;

use App\Api\Resource\FuelCardList;
use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Infrastructure\Criteria\ExportStatusCriteria;
use Pagerfanta\Pagerfanta;

final class DataTransformer implements \App\Api\Crud\Interfaces\DataTransformer
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function transform($data)
    {
        $paginator = $data['paginator'] ?? [];
        $activeCount = (int) $data['activeCount'] ?? 0;
        $limitsCardNumbersOnModeration = $data['limitsCardNumbersOnModeration'] ?? [];

        $collection = [];
        foreach ($paginator as $currentPageResult) {
            $model = new FuelCardList($limitsCardNumbersOnModeration);
            $collection[] = $model->prepare($currentPageResult);
        }

        return [
            'meta' => [
                'pagination' => [
                    'totalCount' => ($paginator instanceof Pagerfanta) ? $paginator->getNbPages() : 1,
                    'currentPage' => ($paginator instanceof Pagerfanta) ? $paginator->getCurrentPage() : 1,
                ],
                'activeCount' => $activeCount,
            ],
            'data' => $collection,
        ];
    }
}
