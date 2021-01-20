<?php

namespace App\Clients\Domain\RegisterToken\UseCase\Delete;

use Domain\Interfaces\Handler as DomainHandler;
use App\Clients\Domain\RegisterToken\Repository\RegisterRepository;
use Doctrine\Common\Persistence\ObjectManager;
use App\Clients\Domain\RegisterToken\Register;
use Domain\Exception\EntityNotFoundException;

final class Handler implements DomainHandler
{
    /**
     * @var RegisterRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        RegisterRepository $repository,
        ObjectManager $objectManager
    ) {
        $this->repository = $repository;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $entity = $this->repository->findById($handlerRequest->getId());
        if (!$entity instanceof Register) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $this->repository->remove($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
