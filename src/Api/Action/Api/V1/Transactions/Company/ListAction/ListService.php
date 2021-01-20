<?php

namespace App\Api\Action\Api\V1\Transactions\Company\ListAction;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Clients\Infrastructure\ClientInfo\Service\Balance\BalanceService;
use Infrastructure\Interfaces\Repository\Repository;

final class ListService implements QueryHandler
{
    private const LIMIT = 20;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var BalanceService
     */
    private $myBalance;

    public function __construct(
        Repository $repository,
        BalanceService $myBalance
    ) {
        $this->repository = $repository;
        $this->myBalance = $myBalance;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $params = $queryRequest->getQueryParams();

        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $offset = ($page - 1) * self::LIMIT;

        $criteria = $queryRequest->getCriteria();
        $result = $this->repository->findMany(
            $criteria,
            $queryRequest->getOrder(),
            self::LIMIT,
            $offset
        );
        $count = $this->repository->count($criteria);

        $accountBalance = $this->myBalance->getBalance();

        return [
            'result' => $result,
            'pageNumber' => $page,
            'countPages' => ($count > 0) ? (int) ceil($count / self::LIMIT) : 1,
            'accountBalance' => $accountBalance,
        ];
    }
}
