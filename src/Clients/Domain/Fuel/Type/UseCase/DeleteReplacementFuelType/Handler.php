<?php

namespace App\Clients\Domain\Fuel\Type\UseCase\DeleteReplacementFuelType;

use App\Clients\Domain\User\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Domain\Interfaces\Handler as DomainHandler;
use Infrastructure\Interfaces\Repository\Repository;

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

        if (!$entity) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $this->repository->remove($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
