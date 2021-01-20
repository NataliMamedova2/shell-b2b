<?php

namespace App\Api\Action\Api\V1\Transactions\Card\ListAction;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Clients\Domain\Transaction\Card\Transaction;
use App\Clients\Infrastructure\ClientInfo\Service\Balance\BalanceService;
use App\Clients\Infrastructure\Fuel\Criteria\IndexByFuelCode;
use Infrastructure\Interfaces\Paginator\Paginator;
use Infrastructure\Interfaces\Repository\Repository;

final class ListService implements QueryHandler
{
    private const LIMIT = 20;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var Repository
     */
    private $transactionRepository;

    /**
     * @var Repository
     */
    private $clientInfoRepository;

    /**
     * @var Repository
     */
    private $fuelTypeRepository;

    /**
     * @var Repository
     */
    private $regionRepository;

    /**
     * @var Repository
     */
    private $networkStationRepository;

    /**
     * @var BalanceService
     */
    private $myBalance;

    public function __construct(
        Paginator $paginator,
        Repository $transactionRepository,
        Repository $clientInfoRepository,
        Repository $fuelTypeRepository,
        Repository $regionRepository,
        Repository $networkStationRepository,
        BalanceService $myBalance
    ) {
        $this->paginator = $paginator;
        $this->transactionRepository = $transactionRepository;
        $this->clientInfoRepository = $clientInfoRepository;
        $this->fuelTypeRepository = $fuelTypeRepository;
        $this->regionRepository = $regionRepository;
        $this->networkStationRepository = $networkStationRepository;
        $this->myBalance = $myBalance;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $params = $queryRequest->getQueryParams();

        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $selectedSuppliesCodes = isset($params['supplies']) ? (array) $params['supplies'] : [];
        $selectedRegionsCodes = isset($params['regions']) ? (array) $params['regions'] : [];
        $selectedNetworkStationsCodes = isset($params['networkStations']) ? (array) $params['networkStations'] : [];

        /** @var Transaction[] $result */
        $result = $this->paginator->paginate(
            $queryRequest->getCriteria(),
            $queryRequest->getOrder(),
            $page,
            self::LIMIT
        );

        $accountBalance = $this->myBalance->getBalance();

        $fuelCodes = [];
        foreach ($result as $transaction) {
            $fuelCodes[] = $transaction->getFuelCode();
        }

        $fuelTypes = $this->fuelTypeRepository->findMany([
            'fuelCode_in' => $fuelCodes,
            IndexByFuelCode::class => true,
        ]);

        $selectedSupplies = [];
        if (!empty($selectedSuppliesCodes)) {
            $selectedSupplies = $this->fuelTypeRepository->findMany([
                'fuelCode_in' => $selectedSuppliesCodes,
            ]);
        }

        $selectedRegions = [];
        if (!empty($selectedRegionsCodes)) {
            $selectedRegions = $this->regionRepository->findMany([
                'code_in' => $selectedRegionsCodes,
            ]);
        }

        $selectedNetworkStations = [];
        if (!empty($selectedNetworkStationsCodes)) {
            $selectedNetworkStations = $this->networkStationRepository->findMany([
                'code_in' => $selectedNetworkStationsCodes,
            ]);
        }

        return [
            'paginator' => $result,
            'accountBalance' => $accountBalance,
            'fuelTypes' => $fuelTypes,
            'selectedSupplies' => $selectedSupplies,
            'selectedRegions' => $selectedRegions,
            'selectedNetworkStations' => $selectedNetworkStations,
        ];
    }
}
