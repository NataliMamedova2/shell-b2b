<?php

namespace App\Api\Action\Api\V1\Transactions\Company\ListAction;

use App\Api\Resource\CompanyTransaction;
use App\Api\Resource\Balance;

final class DataTransformer implements \App\Api\Crud\Interfaces\DataTransformer
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function transform($data)
    {
        $result = $data['result'] ?? [];
        $totalCount = $data['countPages'] ?? 1;
        $currentPage = $data['pageNumber'] ?? 1;

        $collection = [];
        foreach ($result as $currentPageResult) {
            $model = new CompanyTransaction();
            $collection[] = $model->prepare($currentPageResult);
        }

        $balanceModel = new Balance();
        $balance = $balanceModel->prepare($data['accountBalance'] ?? null);

        return [
            'meta' => [
                'pagination' => [
                    'totalCount' => $totalCount,
                    'currentPage' => $currentPage,
                ],
                'accountBalance' => $balance,
            ],
            'data' => $collection,
        ];
    }
}
