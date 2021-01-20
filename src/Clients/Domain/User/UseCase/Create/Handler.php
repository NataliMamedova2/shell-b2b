<?php

namespace App\Clients\Domain\User\UseCase\Create;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\Service\PasswordEncoder;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Name;
use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\UserId;
use App\Clients\Domain\User\ValueObject\Username;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\DomainException;
use Domain\Interfaces\Handler as DomainHandler;

final class Handler implements DomainHandler
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var PasswordEncoder
     */
    private $passwordEncoder;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        UserRepository $repository,
        PasswordEncoder $passwordEncoder,
        ObjectManager $objectManager
    ) {
        $this->repository = $repository;
        $this->passwordEncoder = $passwordEncoder;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        if ($this->repository->findByUsernameOrEmail($handlerRequest->username, $handlerRequest->email)) {
            throw new DomainException('User already exist');
        }

        $passwordHash = $this->passwordEncoder->encode($handlerRequest->password);

        $entity = User::create(
            UserId::next(),
            $handlerRequest->company,
            new Email($handlerRequest->email),
            new Username($handlerRequest->username),
            $passwordHash,
            new Name($handlerRequest->firstName, $handlerRequest->middleName, $handlerRequest->lastName),
            Role::fromName($handlerRequest->role),
            new Phone($handlerRequest->phone)
        );

        $this->repository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
