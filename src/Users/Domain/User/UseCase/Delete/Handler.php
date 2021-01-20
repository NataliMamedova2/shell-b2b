<?php

namespace App\Users\Domain\User\UseCase\Delete;

use App\Users\Domain\User\Exception\DeleteEntityException;
use App\Users\Domain\User\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Domain\Exception\EntityNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

final class Handler implements \Domain\Interfaces\Handler
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserInterface
     */
    private $currentUser;

    public function __construct(
        UserRepository $repository,
        EntityManagerInterface $entityManager,
        UserInterface $currentUser
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->currentUser = $currentUser;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $entity = $this->repository->findById($handlerRequest->getId());

        if (!$entity) {
            throw new EntityNotFoundException('Entity not found.');
        }

        if ($entity->getUsername() === $this->currentUser->getUsername()) {
            throw new DeleteEntityException('You can\'t delete yourself');
        }

        $this->repository->remove($entity);
        $this->entityManager->flush();

        return $entity;
    }
}
