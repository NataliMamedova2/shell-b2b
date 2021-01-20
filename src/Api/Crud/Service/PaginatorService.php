<?php

namespace App\Api\Crud\Service;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use Infrastructure\Interfaces\Paginator\Paginator;

final class PaginatorService implements QueryHandler
{
    private const LIMIT = 20;

    /**
     * @var Paginator
     */
    private $paginator;

    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $params = $queryRequest->getQueryParams();

        $page = isset($params['page']) ? (int) $params['page'] : 1;

        return $this->paginator->paginate(
            $queryRequest->getCriteria(),
            $queryRequest->getOrder(),
            $page,
            self::LIMIT
        );
    }
}
