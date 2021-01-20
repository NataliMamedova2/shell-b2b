<?php

namespace App\Clients\Domain\User\UseCase\UpdateProfile;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\Service\PasswordEncoder;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Name;
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
        /** @var User|null $entity */
        $entity = $handlerRequest->getUser();

        if (!$entity instanceof User) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $userWithUsername = $this->repository->find([
            UsernameOrEmail::class => [
                'email' => $handlerRequest->email,
                'username' => $handlerRequest->username,
            ],
            'id_notEqualTo' => $entity->getId(),
        ]);
        if ($userWithUsername) {
            throw new DomainException('Username or Email already exist');
        }

        $entity->updateProfile(
            new Email($handlerRequest->email),
            new Username($handlerRequest->username),
            new Name($handlerRequest->firstName, $handlerRequest->middleName, $handlerRequest->lastName),
            new Phone($handlerRequest->phone)
        );

        if (!empty($handlerRequest->password)) {
            $passwordHash = $this->passwordEncoder->encode($handlerRequest->password);
            $entity->changePassword($passwordHash);
        }

        $this->repository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
