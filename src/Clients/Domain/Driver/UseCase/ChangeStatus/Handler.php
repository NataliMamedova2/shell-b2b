<?php

namespace App\Clients\Domain\Driver\UseCase\ChangeStatus;

use Domain\Interfaces\Handler as DomainHandler;
use Doctrine\Common\Persistence\ObjectManager;
use Infrastructure\Interfaces\Repository\Repository;
use Domain\Exception\EntityNotFoundException;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\ValueObject\Status;

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

        $status = Status::fromName($handlerRequest->status);
        $entity->changeStatus($status);

        $this->repository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
