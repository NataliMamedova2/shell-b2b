<?php

namespace App\Clients\Domain\Card\UseCase\DeleteDriver;

use App\Clients\Domain\Card\Card;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Domain\Interfaces\Handler as DomainHandler;
use Infrastructure\Interfaces\Repository\Repository;

final class Handler implements DomainHandler
{
    /**
     * @var Repository
     */
    private $cardRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        Repository $cardRepository,
        ObjectManager $objectManager
    ) {
        $this->cardRepository = $cardRepository;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        /** @var Card|null $entity */
        $entity = $this->cardRepository->findById($handlerRequest->getCardId()->getId());

        if (!$entity instanceof Card) {
            throw new EntityNotFoundException('Entity not found.');
        }

        if (null !== $entity->getDriver()) {
            $entity->deleteDriver();
            $this->cardRepository->add($entity);
            $this->objectManager->flush();
        }

        return $entity;
    }
}
