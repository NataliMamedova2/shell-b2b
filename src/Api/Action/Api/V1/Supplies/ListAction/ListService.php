<?php

namespace App\Api\Action\Api\V1\Supplies\ListAction;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Clients\Domain\Fuel\Price\Price;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Infrastructure\Fuel\Criteria\FuelWithPrice;
use Infrastructure\Interfaces\Repository\Repository;

final class ListService implements QueryHandler
{
    /**
     * @var Repository
     */
    private $fuelTypeRepository;

    /**
     * @var Repository
     */
    private $fuelPriceRepository;

    public function __construct(
        Repository $fuelTypeRepository,
        Repository $fuelPriceRepository
    ) {
        $this->fuelTypeRepository = $fuelTypeRepository;
        $this->fuelPriceRepository = $fuelPriceRepository;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $criteria = [
            FuelWithPrice::class => true,
        ];
        /** @var Type[] $fuels */
        $fuels = $this->fuelTypeRepository->findMany($criteria, ['fuelName' => 'ASC']);

        $collection = [];
        foreach ($fuels as $fuel) {
            /** @var Price|null $price */
            $price = $this->fuelPriceRepository->find([
                'fuelCode_equalTo' => $fuel->getFuelCode(),
                'fuelPrice_greaterThan' => 0,
            ]);
            if (!$price instanceof Price) {
                continue;
            }

            $collection[] = [
                'id' => $fuel->getId(),
                'name' => $fuel->getFuelName(),
                'price' => $price->getPriceWithTax(),
            ];
        }

        return $collection;
    }
}
