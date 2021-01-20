<?php

namespace App\Clients\Domain\Driver\UseCase\Update;

use Domain\Interfaces\Handler as DomainHandler;
use Infrastructure\Interfaces\Repository\Repository;
use Doctrine\Common\Persistence\ObjectManager;
use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Driver\ValueObject\Name;
use App\Clients\Domain\Driver\ValueObject\Note;
use App\Clients\Domain\Driver\ValueObject\Status;
use Domain\Exception\EntityNotFoundException;
use App\Clients\Domain\Driver\Driver;

final class Handler implements DomainHandler
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        Repository $repository,
        ObjectManager $objectManager
    ) {
        $this->repository = $repository;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $entity = $this->repository->findById($handlerRequest->getId());

        if (!$entity instanceof Driver) {
            throw new EntityNotFoundException('Entity not found.');
        }

        array_walk($handlerRequest->phones, function (&$v) {
            $v = $v['number'];
        });
        array_walk($handlerRequest->carsNumbers, function (&$v) {
            $v = $v['number'];
        });

        $dateTime = new \DateTimeImmutable();
        $email = (!empty($handlerRequest->email)) ? new Email($handlerRequest->email) : null;
        $note = (!empty($handlerRequest->note)) ? new Note($handlerRequest->note) : null;

        $entity->update(
            new Name($handlerRequest->firstName, $handlerRequest->middleName, $handlerRequest->lastName),
            Status::fromName($handlerRequest->status),
            $handlerRequest->phones,
            $handlerRequest->carsNumbers,
            $dateTime,
            $email,
            $note
        );

        $this->repository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
