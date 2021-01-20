<?php

namespace App\Api\Action\Api\V1\Transactions\Card\CreateReportAction\Service;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Clients\Domain\Transaction\Card\Transaction;
use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use App\Clients\Infrastructure\Fuel\Criteria\IndexByFuelCode;
use App\Clients\Infrastructure\Transaction\Repository\Repository as TransactionRepository;
use Infrastructure\Interfaces\Repository\Repository;

final class GetReportTransactionsHandler implements QueryHandler
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var Repository
     */
    private $fuelTypeRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        Repository $fuelTypeRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->fuelTypeRepository = $fuelTypeRepository;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $baseCriteria = $queryRequest->getCriteria();
        $order = $queryRequest->getOrder();

        $typeCriteria = [
            'type_notEqualTo' => [
                Type::replenishment()->getValue(),
            ],
        ];
        $criteria = array_merge($baseCriteria, $typeCriteria);
        /** @var Transaction[] $transactions */
        $transactions = $this->transactionRepository->findMany($criteria, $order);

        $replenishmentCriteria = [
            'type_notIn' => [
                Type::writeOff()->getValue(),
                Type::return()->getValue(),
            ],
        ];
        $criteria = array_merge($baseCriteria, $replenishmentCriteria);
        /** @var Transaction[] $replenishmentTransactions */
        $replenishmentTransactions = $this->transactionRepository->findMany($criteria, $order);

        $fuelCodes = [];
        foreach ($transactions as $transaction) {
            $fuelCodes[$transaction->getFuelCode()] = $transaction->getFuelCode();
        }
        foreach ($replenishmentTransactions as $transaction) {
            $fuelCodes[$transaction->getFuelCode()] = $transaction->getFuelCode();
        }
        if (\is_array($queryRequest->getQueryParams())) {
            if (true === isset($queryRequest->getQueryParams()['supplies'])) {
                $fuelSupplies = $queryRequest->getQueryParams()['supplies'];
                $fuelDiff = \array_diff_key(\array_values($fuelSupplies), $fuelCodes);
                if ($fuelDiff) {
                    $fuelCodes = \array_merge($fuelDiff, $fuelCodes);
                }
            }
        }

        $fuelTypes = $this->fuelTypeRepository->findMany([
            'fuelCode_in' => array_values($fuelCodes),
            IndexByFuelCode::class => true,
        ]);

        return [
            'transactions' => $transactions,
            'replenishmentTransactions' => $replenishmentTransactions,
            'fuelTypes' => $fuelTypes,
        ];
    }
}
