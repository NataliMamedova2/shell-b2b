<?php

namespace App\Api\Action\Api\V1\Invoice\CreditDebtAction;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Infrastructure\ClientInfo\Criteria\ByClient;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;

final class CreditDebtService implements QueryHandler
{
    /**
     * @var MyselfInterface
     */
    private $myself;

    /**
     * @var Repository
     */
    private $clientInfoRepository;

    public function __construct(MyselfInterface $myself, Repository $clientInfoRepository)
    {
        $this->myself = $myself;
        $this->clientInfoRepository = $clientInfoRepository;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $clientInfo = $this->clientInfoRepository->find([
            ByClient::class => $this->myself->getClient(),
        ]);

        if (!$clientInfo instanceof ClientInfo || $clientInfo->getBalance() >= 0) {
            return [
                'amount' => 0,
            ];
        }

        $accountBalance = (int) round(abs($clientInfo->getBalance()));

        return [
            'amount' => $accountBalance,
        ];
    }
}
