<?php

namespace App\Clients\Domain\User\UseCase\RecoverPass;

use App\Clients\Domain\User\Service\PasswordEncoder;
use App\Clients\Domain\User\User;
use Domain\Exception\EntityNotFoundException;
use Domain\Interfaces\Handler as DomainHandler;
use App\Clients\Domain\User\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;

final class Handler implements DomainHandler
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var PasswordEncoder
     */
    private $passwordEncoder;

    public function __construct(
        UserRepository $repository,
        PasswordEncoder $passwordEncoder,
        ObjectManager $entityManager
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $entity = $this->repository->findByToken($handlerRequest->token);

        if (!$entity instanceof User) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $passwordHash = $this->passwordEncoder->encode($handlerRequest->password);

        $entity->changePassword($passwordHash);

        $entity->clearRestoreToken();

        $this->repository->add($entity);
        $this->entityManager->flush();

        return $entity;
    }
}
