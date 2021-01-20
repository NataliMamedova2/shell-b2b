<?php

namespace App\Clients\Domain\User\UseCase\ChangeStatus;

use App\Clients\Domain\User\ValueObject\Status;
use Domain\Exception\EntityNotFoundException;
use Domain\Interfaces\Handler as DomainHandler;
use App\Clients\Domain\User\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use App\Clients\Domain\User\User;

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
        /** @var User|null $entity */
        $entity = $this->repository->findById($handlerRequest->getId());

        if (!$entity instanceof User) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $entity->changeStatus(Status::fromName($handlerRequest->status));

        $this->repository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
