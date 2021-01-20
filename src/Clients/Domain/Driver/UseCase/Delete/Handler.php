<?php

namespace App\Clients\Domain\Driver\UseCase\Delete;

use App\Clients\Domain\Driver\Driver;
use Domain\Interfaces\Handler as DomainHandler;
use Infrastructure\Interfaces\Repository\Repository;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;

final class Handler implements DomainHandler
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        Repository $repository,
        ObjectManager $objectManager
    ) {
        $this->repository = $repository;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $entity = $this->repository->findById($handlerRequest->getId());

        if (!$entity instanceof Driver) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $this->repository->remove($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
