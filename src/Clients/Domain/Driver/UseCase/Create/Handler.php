<?php

namespace App\Clients\Domain\Driver\UseCase\Create;

use App\Clients\Domain\Driver\ValueObject\CarNumber;
use App\Clients\Domain\Driver\ValueObject\Name;
use App\Clients\Domain\Driver\ValueObject\Note;
use App\Clients\Domain\Driver\ValueObject\Status;
use Domain\Interfaces\Handler as DomainHandler;
use Infrastructure\Interfaces\Repository\Repository;
use Doctrine\Common\Persistence\ObjectManager;
use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\ValueObject\DriverId;

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
        $dateTime = new \DateTimeImmutable();
        $email = (!empty($handlerRequest->email)) ? new Email($handlerRequest->email) : null;
        $note = (!empty($handlerRequest->note)) ? new Note($handlerRequest->note) : null;

        array_walk($handlerRequest->phones, function (&$v) {
            $v = $v['number'];
        });
        array_walk($handlerRequest->carsNumbers, function (&$v) {
            $v = $v['number'];
        });

        $entity = Driver::create(
            DriverId::next(),
            $handlerRequest->client,
            new Name($handlerRequest->firstName, $handlerRequest->middleName, $handlerRequest->lastName),
            Status::fromName($handlerRequest->status),
            $handlerRequest->phones,
            $dateTime,
            $email,
            $note
        );

        if (!empty($handlerRequest->carsNumbers)) {
            foreach ($handlerRequest->carsNumbers as $carNumber) {
                $entity->addCarNumber(new CarNumber($carNumber), $dateTime);
            }
        }

        $this->repository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
