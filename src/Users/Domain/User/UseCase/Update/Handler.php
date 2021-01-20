<?php

namespace App\Users\Domain\User\UseCase\Update;

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Client\ValueObject\Manager1CId;
use App\Users\Domain\User\Repository\UserRepository;
use App\Users\Domain\User\Service\HashPasswordService;
use App\Users\Domain\User\User;
use App\Users\Domain\User\ValueObject\Avatar;
use App\Users\Domain\User\ValueObject\FullName;
use App\Users\Domain\User\ValueObject\Phone;
use App\Users\Domain\User\ValueObject\Role;
use App\Users\Domain\User\ValueObject\Status;
use App\Users\Domain\User\ValueObject\Username;
use App\Users\Infrastructure\Criteria\UsernameOrEmail;
use Doctrine\ORM\EntityManagerInterface;
use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;

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
     * @var HashPasswordService
     */
    private $hashPassword;

    public function __construct(
        UserRepository $repository,
        EntityManagerInterface $entityManager,
        HashPasswordService $hashPassword
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->hashPassword = $hashPassword;
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

        $avatar = new Avatar(
            $handlerRequest->avatar['path'] ?? '',
            $handlerRequest->avatar['fileName'] ?? '',
            $handlerRequest->avatar['cropData'] ?? []
        );

        $entity->update(
            new Email($handlerRequest->email),
            new Username($handlerRequest->username),
            new FullName($handlerRequest->name),
            new Role($handlerRequest->role),
            new Status($handlerRequest->status),
            new Phone($handlerRequest->phone),
            $avatar,
            new Manager1CId($handlerRequest->manager1CId)
        );

        if (!empty($handlerRequest->password)) {
            $passwordHash = $this->hashPassword->encode($entity, $handlerRequest->password);
            $entity->changePassword($passwordHash);
        }

        $this->repository->add($entity);
        $this->entityManager->flush();

        return $entity;
    }
}
