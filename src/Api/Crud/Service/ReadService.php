<?php

namespace App\Api\Crud\Service;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ReadService implements QueryHandler
{
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
        $entity = $this->repository->find(
            $queryRequest->getCriteria(),
            $queryRequest->getOrder()
        );

        if (empty($entity)) {
            throw new NotFoundHttpException('Entity not found');
        }

        return $entity;
    }
}
