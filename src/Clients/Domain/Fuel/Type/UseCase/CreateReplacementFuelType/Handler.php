<?php

namespace App\Clients\Domain\Fuel\Type\UseCase\CreateReplacementFuelType;

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Type\ReplacementFuelType;
use App\Clients\Domain\Fuel\Type\ValueObject\RepacementTypeId;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Interfaces\Handler as DomainHandler;
use Infrastructure\Interfaces\Repository\Repository;

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
        $entity = ReplacementFuelType::create(
            RepacementTypeId::next(),
            new FuelCode($handlerRequest->fuelCode),
            new FuelCode($handlerRequest->fuelReplacementCode)
        );

        $this->repository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
