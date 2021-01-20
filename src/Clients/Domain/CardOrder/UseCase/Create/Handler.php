<?php

namespace App\Clients\Domain\CardOrder\UseCase\Create;

use App\Application\Domain\ValueObject\IdentityId;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\CardOrder\Order;
use Doctrine\Common\Persistence\ObjectManager;
use Infrastructure\Interfaces\Repository\Repository;

final class Handler implements \Domain\Interfaces\Handler
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    public function __construct(
        Repository $repository,
        ObjectManager $entityManager
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $entity = new Order(
            IdentityId::next(),
            $handlerRequest->user,
            $handlerRequest->count,
            $handlerRequest->name,
            new Phone($handlerRequest->phone),
            new \DateTimeImmutable()
        );

        $this->repository->add($entity);
        $this->entityManager->flush();

        return $entity;
    }
}
