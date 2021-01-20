<?php

declare(strict_types=1);

namespace App\Api\Domain\Log\UseCase\Create;

use App\Api\Domain\Log\Log;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Repository\Repository;

final class Handler
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Repository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function handle(HandlerRequest $handlerRequest): void
    {
        $entity = Log::create(
            $handlerRequest->resource,
            $handlerRequest->request,
            $handlerRequest->response,
            $handlerRequest->IPAddress
        );

        $this->repository->add($entity);
        $this->entityManager->flush();
    }
}
