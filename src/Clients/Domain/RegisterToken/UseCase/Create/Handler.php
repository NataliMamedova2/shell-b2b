<?php

namespace App\Clients\Domain\RegisterToken\UseCase\Create;

use Domain\Interfaces\Handler as DomainHandler;
use App\Clients\Domain\RegisterToken\Repository\RegisterRepository;
use App\Clients\Domain\RegisterToken\Service\TokenGenerator;
use Doctrine\Common\Persistence\ObjectManager;
use App\Clients\Domain\RegisterToken\Register;
use App\Clients\Domain\RegisterToken\ValueObject\RegisterId;
use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\RegisterToken\ValueObject\Token;
use App\Clients\Domain\RegisterToken\ValueObject\ContractNumber;
use Domain\Exception\DomainException;

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
        $registerToken = $this->repository->find(['client_equalTo' => $handlerRequest->client]);

        if ($registerToken instanceof Register) {
            throw new DomainException(sprintf('Token for client "%s" already exist', $handlerRequest->client->getFullName()));
        }

        $token = $this->tokenGenerator->generate();

        $entity =  Register::create(
            RegisterId::next(),
            $handlerRequest->client,
            new Email($handlerRequest->email),
            new Token($token),
            new \DateTimeImmutable()
        );

        $this->repository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
