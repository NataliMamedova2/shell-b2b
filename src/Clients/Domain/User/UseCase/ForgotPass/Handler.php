<?php

namespace App\Clients\Domain\User\UseCase\ForgotPass;

use App\Clients\Domain\User\Service\TokenGenerator;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Token;
use App\Clients\Infrastructure\User\Criteria\Login;
use App\Mailer\Template;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Domain\Interfaces\Handler as DomainHandler;
use Infrastructure\Interfaces\Repository\Repository;
use MailerBundle\Interfaces\Sender;

final class Handler implements DomainHandler
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var Sender
     */
    private $sender;

    public function __construct(
        Repository $repository,
        ObjectManager $entityManager,
        TokenGenerator $tokenGenerator,
        Sender $sender
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->sender = $sender;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        /** @var User $entity */
        $entity = $this->repository->find([
            Login::class => $handlerRequest->username,
        ]);

        if (!$entity instanceof User) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $token = $this->tokenGenerator->generate();

        $entity->setRestoreToken(new Token($token));

        $this->repository->add($entity);
        $this->entityManager->flush();

        $this->sender->send($entity->getEmail(), Template::FORGOT_PASS, [
            'token' => $token,
        ]);

        return $entity;
    }
}
