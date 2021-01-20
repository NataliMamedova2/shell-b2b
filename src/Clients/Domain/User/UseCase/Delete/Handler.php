<?php

namespace App\Clients\Domain\User\UseCase\Delete;

use App\Clients\Domain\User\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Domain\Interfaces\Handler as DomainHandler;

final class Handler implements DomainHandler
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        UserRepository $repository,
        ObjectManager $objectManager
    ) {
        $this->repository = $repository;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $entity = $this->repository->findById($handlerRequest->getId());

        if (!$entity) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $this->repository->remove($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
