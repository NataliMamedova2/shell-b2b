<?php

namespace App\Api\Action\Api\V1\Transactions\Card\ListAction;

use App\Api\Resource\Balance;
use App\Api\Resource\CardTransaction;
use App\Api\Resource\Model;
use App\Api\Resource\NetworkStation;
use App\Api\Resource\Supply;
use App\Api\Resource\TransactionRegion;
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
        $fuelTypes = (array) $data['fuelTypes'] ?? [];
        $selectedSupplies = (array) $data['selectedSupplies'] ?? [];
        $selectedRegions = (array) $data['selectedRegions'] ?? [];
        $selectedNetworkStations = (array) $data['selectedNetworkStations'] ?? [];

        $collection = [];
        foreach ($paginator as $currentPageResult) {
            $model = new CardTransaction($fuelTypes);
            $collection[] = $model->prepare($currentPageResult);
        }

        $balanceModel = new Balance();
        $balance = $balanceModel->prepare($data['accountBalance'] ?? null);

        $filters = [
            'supplies' => $this->prepareFilterData($selectedSupplies, new Supply()),
            'regions' => $this->prepareFilterData($selectedRegions, new TransactionRegion()),
            'networkStations' => $this->prepareFilterData($selectedNetworkStations, new NetworkStation()),
        ];

        return [
            'meta' => [
                'pagination' => [
                    'totalCount' => ($paginator instanceof Pagerfanta) ? $paginator->getNbPages() : 1,
                    'currentPage' => ($paginator instanceof Pagerfanta) ? $paginator->getCurrentPage() : 1,
                ],
                'accountBalance' => $balance,
                'filters' => $filters,
            ],
            'data' => $collection,
        ];
    }

    /**
     * @param array $array
     * @param Model $class
     *
     * @return Model[]
     */
    private function prepareFilterData(array $array, Model $class): array
    {
        $collection = [];
        foreach ($array as $item) {
            $model = clone $class;
            $collection[] = $model->prepare($item);
        }

        return $collection;
    }
}
