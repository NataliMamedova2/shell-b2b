<?php

namespace App\Api\Action\Api\V1\Transactions\Company\CreateReportAction\Service;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use Infrastructure\Interfaces\Repository\Repository;

final class GetReportTransactionsHandler implements QueryHandler
{
    /** @var Repository */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $criteria = $queryRequest->getCriteria();
        $order = $queryRequest->getOrder();

        $transactions = $this->repository->findMany($criteria, $order);

        return [
            'transactions' => $transactions,
        ];
    }
}
