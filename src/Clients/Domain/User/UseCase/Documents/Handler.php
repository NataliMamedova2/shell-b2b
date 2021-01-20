<?php

namespace App\Clients\Domain\User\UseCase\Documents;

use App\Users\Domain\User\User;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Interfaces\Handler as DomainHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class Handler implements DomainHandler
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * Handler constructor.
     * @param ObjectManager $objectManager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(ObjectManager $objectManager, TokenStorageInterface $tokenStorage)
    {
        $this->objectManager = $objectManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function handle(HandlerRequest $handlerRequest, User $user): void
    {
        if (false === empty($handlerRequest->token)) {
            $user->setSotaToken($handlerRequest->token);

            $this->objectManager->flush();
        }
    }
}
