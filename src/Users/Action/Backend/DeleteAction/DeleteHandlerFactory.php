<?php

namespace App\Users\Action\Backend\DeleteAction;

use App\Users\Domain\User\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Users\Domain\User\UseCase\Delete\Handler;

final class DeleteHandlerFactory
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
     * @var Security
     */
    private $security;

    public function __construct(
        UserRepository $repository,
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function __invoke(): Handler
    {
        $user = $this->security->getUser();

        return new Handler($this->repository, $this->entityManager, $user);
    }
}
