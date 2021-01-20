<?php

namespace App\Api\Action\Api\V1\Dashboard;

use App\Api\Crud\Interfaces\Response as JsonResponse;
use App\Api\Resource\Balance;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Domain\RefillBalance\RefillBalance;
use App\Clients\Infrastructure\ClientInfo\Criteria\ByClient;
use App\Clients\Infrastructure\ClientInfo\Service\Balance\BalanceService;
use App\Clients\Infrastructure\Discount\Repository\DiscountRepository;
use App\Clients\Infrastructure\Transaction\Repository\TransactionRepository;
use App\Security\Cabinet\MyselfInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Routing\Annotation\Route;

final class InfoAction
{
    /**
     * @var MyselfInterface
     */
    private $myself;
    /**
     * @var BalanceService
     */
    private $myBalance;
    /**
     * @var Repository
     */
    private $clientInfoRepository;
    /**
     * @var Repository
     */
    private $refillBalanceRepository;
    /**
     * @var DiscountRepository
     */
    private $discountRepository;
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;
    /**
     * @var JsonResponse
     */
    private $jsonResponse;

    public function __construct(
        MyselfInterface $myself,
        BalanceService $myBalance,
        Repository $clientInfoRepository,
        Repository $refillBalanceRepository,
        DiscountRepository $discountRepository,
        TransactionRepository $transactionRepository,
        JsonResponse $jsonResponse
    ) {
        $this->myself = $myself;
        $this->myBalance = $myBalance;
        $this->clientInfoRepository = $clientInfoRepository;
        $this->refillBalanceRepository = $refillBalanceRepository;
        $this->discountRepository = $discountRepository;
        $this->transactionRepository = $transactionRepository;
        $this->jsonResponse = $jsonResponse;
    }

    /**
     * @Route(
     *     "/api/v1/dashboard",
     *     name="api_v1_dashboard",
     *     methods={"GET"}
     * )
     *
     * @throws \Exception
     */
    public function __invoke(): SymfonyResponse
    {
        $client = $this->myself->getClient();

        /** @var ClientInfo|null $clientInfo */
        $clientInfo = $this->clientInfoRepository->find([
            ByClient::class => $client,
        ]);

        $clientBalance = $this->myBalance->getBalance();
        $creditLimit = $clientInfo instanceof ClientInfo ? $clientInfo->getCreditLimit() : 0;

        /** @var RefillBalance|null $lastBalanceUpdate */
        $lastBalanceUpdate = $this->refillBalanceRepository->find([
            'fcCbrId_equalTo' => $client->getClientPcId(),
        ], ['operationDate' => 'DESC']);

        $balanceUpdate = null;
        if ($lastBalanceUpdate instanceof RefillBalance && !empty($lastBalanceUpdate->getAmount())) {
            $balanceUpdate = [
                'balance' => [
                    'value' => $lastBalanceUpdate->getAmount(),
                    'sign' => $lastBalanceUpdate->getOperationSign(),
                ],
                'dateTime' => $lastBalanceUpdate->getOperationDateTime(),
            ];
        }

        if ($clientBalance->getValue() > 0) {
            $availableBalance = $creditLimit + $clientBalance->getAbsoluteValue();
        } else {
            $availableBalance = $creditLimit - $clientBalance->getAbsoluteValue();
        }

        $date = new \DateTimeImmutable('-1 month');
        $lastMonthDiscountSum = $this->discountRepository->calculateSum(
            $client,
            new \DateTimeImmutable($date->format('Y-m-01 00:00:00')),
            new \DateTimeImmutable($date->format('Y-m-t 23:59:59'))
        );

        $cardStatistic = $this->getCardsStatistic($client);

        $balanceModel = new Balance();
        $data = [
            'balance' => $balanceModel->prepare($clientBalance),
            'balanceUpdate' => $balanceUpdate,
            'creditLimit' => $creditLimit,
            'availableBalance' => $availableBalance,
            'lastMonthDiscountSum' => $lastMonthDiscountSum,
            'cardsStatistic' => $cardStatistic,
        ];

        return $this->jsonResponse->createSuccessResponse($data);
    }

    /**
     * @param Client $client
     *
     * @return array|null
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function getCardsStatistic(Client $client): ?array
    {
        $date = new \DateTimeImmutable('today');
        $dayTransactionsSum = $this->transactionRepository->calculateClientDebitSum(
            $client,
            new \DateTimeImmutable('today'),
            new \DateTimeImmutable('tomorrow')
        );

        $weekTransactionsSum = $this->transactionRepository->calculateClientDebitSum(
            $client,
            new \DateTimeImmutable('monday this week'),
            new \DateTimeImmutable('monday next week')
        );

        $monthTransactionsSum = $this->transactionRepository->calculateClientDebitSum(
            $client,
            new \DateTimeImmutable($date->format('Y-m-01')),
            new \DateTimeImmutable($date->format('Y-m-t'))
        );

        if (empty($dayTransactionsSum) && empty($weekTransactionsSum) && empty($monthTransactionsSum)) {
            return null;
        }

        return [
            'day' => $dayTransactionsSum,
            'week' => $weekTransactionsSum,
            'month' => $monthTransactionsSum,
        ];
    }
}
