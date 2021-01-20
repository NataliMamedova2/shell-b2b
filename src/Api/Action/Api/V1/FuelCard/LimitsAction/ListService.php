<?php

namespace App\Api\Action\Api\V1\FuelCard\LimitsAction;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Api\DataTransformer\CardReadDataTransformer;
use App\Clients\Domain\FuelLimit\FuelLimit;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use App\Clients\Infrastructure\Transaction\Repository\TransactionRepository;
use App\Security\Cabinet\Myself;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ListService implements QueryHandler
{
    /**
     * @var Repository
     */
    private $cardLimitsRepository;

    /**
     * @var Repository
     */
    private $fuelCardRepository;

    /**
     * @var Repository
     */
    private $fuelTypeRepository;

    /**
     * @var Myself
     */
    private $myself;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var array
     */
    private $fuelTypes;
    /**
     * @var CardReadDataTransformer
     */
    private $dataTransformer;

    public function __construct(
        Repository $cardLimitsRepository,
        Repository $fuelCardRepository,
        Repository $fuelTypeRepository,
        TransactionRepository $transactionRepository,
        CardReadDataTransformer $dataTransformer,
        Myself $myself
    ) {
        $this->cardLimitsRepository = $cardLimitsRepository;
        $this->fuelCardRepository = $fuelCardRepository;
        $this->fuelTypeRepository = $fuelTypeRepository;
        $this->transactionRepository = $transactionRepository;
        $this->dataTransformer = $dataTransformer;
        $this->myself = $myself;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $params = $queryRequest->getQueryParams();

        $client = $this->myself->getClient();
        $card = $this->fuelCardRepository->find([
            'id_equalTo' => $params['cardId'],
            'client1CId_equalTo' => $client->getClient1CId(),
        ]);

        if (!$card instanceof Card) {
            throw new NotFoundHttpException('Card not found');
        }

        $cardNumber = $card->getCardNumber();
        $clientId = $client->getClient1CId();

        $criteria = array_merge([
            'client1CId_equalTo' => $clientId,
            'cardNumber_equalTo' => $cardNumber,
            'purseActivity_equalTo' => PurseActivity::active()->getValue(),
        ], $queryRequest->getCriteria());

        $this->fuelTypes = $this->fuelTypeRepository->findMany([]);

        $result = $this->cardLimitsRepository->findMany($criteria, $queryRequest->getOrder());

        $date = new \DateTimeImmutable('today');

        $dayTransactionsSum = $this->transactionRepository->calculateDebitSum(
            $clientId,
            $cardNumber,
            new \DateTimeImmutable('today'),
            new \DateTimeImmutable('tomorrow')
        );

        $weekTransactionsSum = $this->transactionRepository->calculateDebitSum(
            $clientId,
            $cardNumber,
            new \DateTimeImmutable('monday this week'),
            new \DateTimeImmutable('monday next week')
        );

        $monthTransactionsSum = $this->transactionRepository->calculateDebitSum(
            $clientId,
            $cardNumber,
            new \DateTimeImmutable($date->format('Y-m-01')),
            new \DateTimeImmutable($date->format('Y-m-t'))
        );

        $moneyLimits = [
            'name' => 'Гривня',
            'day' => [
                'total' => $card->getDayLimit(),
                'left' => $card->getDayLimit() - $dayTransactionsSum,
            ],
            'week' => [
                'total' => $card->getWeekLimit(),
                'left' => $card->getWeekLimit() - $weekTransactionsSum,
            ],
            'month' => [
                'total' => $card->getMonthLimit(),
                'left' => $card->getMonthLimit() - $monthTransactionsSum,
            ],
        ];

        $collection = [];
        /** @var FuelLimit $fuelLimit */
        foreach ($result as $fuelLimit) {
            $dayTransactionsSum = $this->transactionRepository->calculateFuelQuantitySum(
                $clientId,
                $cardNumber,
                $fuelLimit->getFuelCode(),
                new \DateTimeImmutable('today'),
                new \DateTimeImmutable('tomorrow')
            );

            $weekTransactionsSum = $this->transactionRepository->calculateFuelQuantitySum(
                $clientId,
                $cardNumber,
                $fuelLimit->getFuelCode(),
                new \DateTimeImmutable('monday this week'),
                new \DateTimeImmutable('monday next week')
            );

            $monthTransactionsSum = $this->transactionRepository->calculateFuelQuantitySum(
                $clientId,
                $cardNumber,
                $fuelLimit->getFuelCode(),
                new \DateTimeImmutable($date->format('Y-m-01')),
                new \DateTimeImmutable($date->format('Y-m-t'))
            );

            $collection[] = [
                'id' => $fuelLimit->getId(),
                'name' => $this->getFuelNameByCode($fuelLimit->getFuelCode()),
                'day' => [
                    'total' => $fuelLimit->getDayLimit(),
                    'left' => $fuelLimit->getDayLimit() - $dayTransactionsSum,
                ],
                'week' => [
                    'total' => $fuelLimit->getWeekLimit(),
                    'left' => $fuelLimit->getWeekLimit() - $weekTransactionsSum,
                ],
                'month' => [
                    'total' => $fuelLimit->getMonthLimit(),
                    'left' => $fuelLimit->getMonthLimit() - $monthTransactionsSum,
                ],
            ];
        }

        return [
            'card' => $this->dataTransformer->transform($card),
            'moneyLimits' => $moneyLimits,
            'limits' => $collection,
        ];
    }

    private function getFuelNameByCode(string $fuelCode): string
    {
        $fuelName = '';
        foreach ($this->fuelTypes as $fuelType) {
            if ($fuelType->getFuelCode() == $fuelCode) {
                $fuelName = $fuelType->getFuelName();
            }
        }

        return $fuelName;
    }
}
