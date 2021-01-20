<?php

namespace App\Clients\Infrastructure\ClientInfo\Service\Balance;

use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Infrastructure\ClientInfo\Criteria\ByClient;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;

final class MyBalanceService implements BalanceService
{
    /**
     * @var Repository
     */
    private $clientInfoRepository;

    /**
     * @var MyselfInterface
     */
    private $myself;

    /**
     * @var ClientInfo|null
     */
    private $clientInfo;

    public function __construct(
        Repository $clientInfoRepository,
        MyselfInterface $myself
    ) {
        $this->clientInfoRepository = $clientInfoRepository;
        $this->myself = $myself;
    }

    public function getBalance(): Balance
    {
        $clientInfo = $this->getClientInfo();

        $value = (null !== $clientInfo) ? $clientInfo->getBalance() : 0;

        return new Balance($value);
    }

    private function getClientInfo(): ?ClientInfo
    {
        if ($this->clientInfo instanceof ClientInfo) {
            return $this->clientInfo;
        }

        $client = $this->myself->getClient();
        /** @var ClientInfo|null $clientInfo */
        $clientInfo = $this->clientInfoRepository->find([
            ByClient::class => $client,
        ]);

        $this->clientInfo = $clientInfo;

        return $this->clientInfo;
    }
}
