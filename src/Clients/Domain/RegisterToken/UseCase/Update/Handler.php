<?php

namespace App\Clients\Domain\RegisterToken\UseCase\Update;

use Domain\Interfaces\Handler as DomainHandler;
use App\Clients\Domain\RegisterToken\Repository\RegisterRepository;
use App\Clients\Domain\RegisterToken\Service\TokenGenerator;
use Doctrine\Common\Persistence\ObjectManager;
use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\RegisterToken\Register;
use App\Clients\Domain\RegisterToken\ValueObject\ContractNumber;
use App\Clients\Domain\RegisterToken\ValueObject\Token;
use Domain\Exception\EntityNotFoundException;

final class Handler implements DomainHandler
{
    /**
     * @var RegisterRepository
     */
    private $repository;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        RegisterRepository $repository,
        TokenGenerator $tokenGenerator,
        ObjectManager $objectManager
    ) {
        $this->repository = $repository;
        $this->tokenGenerator = $tokenGenerator;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $entity = $this->repository->findById($handlerRequest->getId());
        if (!$entity instanceof Register) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $token = $this->tokenGenerator->generate();

        $entity->update(
            new Email($handlerRequest->email),
            new Token($token)
        );

        $this->repository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
