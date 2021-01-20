<?php

namespace App\Clients\Domain\User\UseCase\Update;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\Service\PasswordEncoder;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Name;
use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\Username;
use App\Users\Infrastructure\Criteria\UsernameOrEmail;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;
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
    private $entityManager;

    public function __construct(
        UserRepository $repository,
        PasswordEncoder $passwordEncoder,
        ObjectManager $entityManager
    ) {
        $this->repository = $repository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        /** @var User|null $entity */
        $entity = $this->repository->findById($handlerRequest->getId());

        if (!$entity instanceof User) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $userWithUsername = $this->repository->find([
            UsernameOrEmail::class => [
                'email' => $handlerRequest->email,
                'username' => $handlerRequest->username,
            ],
            'id_notEqualTo' => $handlerRequest->getId(),
        ]);
        if ($userWithUsername) {
            throw new DomainException('Username or Email already exist');
        }

        $entity->update(
            new Email($handlerRequest->email),
            new Username($handlerRequest->username),
            new Name($handlerRequest->firstName, $handlerRequest->middleName, $handlerRequest->lastName),
            Role::fromName($handlerRequest->role),
            new Phone($handlerRequest->phone)
        );

        if (!empty($handlerRequest->password)) {
            $passwordHash = $this->passwordEncoder->encode($handlerRequest->password);
            $entity->changePassword($passwordHash);
        }

        $this->entityManager->flush();

        return $entity;
    }
}
