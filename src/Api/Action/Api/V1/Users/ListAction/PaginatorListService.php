<?php

namespace App\Api\Action\Api\V1\Users\ListAction;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Clients\Domain\User\ValueObject\Status;
use App\Security\Cabinet\Myself;
use Infrastructure\Interfaces\Paginator\Paginator;
use Infrastructure\Interfaces\Repository\Repository;

final class PaginatorListService implements QueryHandler
{
    private const LIMIT = 20;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var Myself
     */
    private $myself;

    public function __construct(Paginator $paginator, Repository $repository, Myself $myself)
    {
        $this->paginator = $paginator;
        $this->repository = $repository;
        $this->myself = $myself;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $params = $queryRequest->getQueryParams();

        $page = isset($params['page']) ? (int) $params['page'] : 1;

        $result = $this->paginator->paginate(
            $queryRequest->getCriteria(),
            $queryRequest->getOrder(),
            $page,
            self::LIMIT
        );

        $myself = $this->myself->get();
        $company = $myself->getCompany();

        $activeCount = $this->repository->count([
            'company_equalTo' => $company,
            'id_notEqualTo' => $myself->getId(),
            'status_equalTo' => Status::active()->getValue(),
        ]);
        $blockedCount = $this->repository->count([
            'company_equalTo' => $company,
            'id_notEqualTo' => $myself->getId(),
            'status_equalTo' => Status::blocked()->getValue(),
        ]);
        $totalCount = $this->repository->count([
            'company_equalTo' => $company,
            'id_notEqualTo' => $myself->getId(),
        ]);

        return [
            'paginator' => $result,
            'totalCount' => $totalCount,
            'activeCount' => $activeCount,
            'blockedCount' => $blockedCount,
        ];
    }
}
