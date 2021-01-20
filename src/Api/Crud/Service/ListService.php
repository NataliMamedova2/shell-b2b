<?php

namespace App\Api\Crud\Service;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use Infrastructure\Interfaces\Repository\Repository;

final class ListService implements QueryHandler
{
    private const LIMIT = 10;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $params = $queryRequest->getQueryParams();

        $limit = isset($params['limit']) ? (int) $params['limit'] : self::LIMIT;
        $offset = isset($params['offset']) ? (int) $params['offset'] : 0;

        return $this->repository->findMany(
            $queryRequest->getCriteria(),
            $queryRequest->getOrder(),
            $limit,
            $offset
        );
    }
}
