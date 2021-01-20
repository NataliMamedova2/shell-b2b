<?php

namespace App\Api\Action\Api\V1\Users\ListAction;

use App\Api\Resource\CompanyUser;
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
        $totalCount = (int) $data['totalCount'] ?? 0;
        $activeCount = (int) $data['activeCount'] ?? 0;
        $blockedCount = (int) $data['blockedCount'] ?? 0;

        $collection = [];
        foreach ($paginator as $currentPageResult) {
            $model = new CompanyUser();
            $collection[] = $model->prepare($currentPageResult);
        }

        $result = [
            'meta' => [
                'pagination' => [
                    'totalCount' => ($paginator instanceof Pagerfanta) ? $paginator->getNbPages() : 0,
                    'currentPage' => ($paginator instanceof Pagerfanta) ? $paginator->getCurrentPage() : 1,
                ],
                'totalCount' => $totalCount,
                'activeCount' => $activeCount,
                'blockedCount' => $blockedCount,
            ],
            'data' => $collection,
        ];

        return $result;
    }
}
