<?php

namespace App\Clients\Domain\Card\UseCase\Update;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Infrastructure\Criteria\ExportStatusCriteria;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Card\ValueObject\MoneyLimits;
use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use App\Clients\Domain\Card\ValueObject\TimeUse;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\FuelLimit\FuelLimit;
use App\Clients\Domain\FuelLimit\ValueObject\Limits;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Domain\Interfaces\Handler as DomainHandler;
use Infrastructure\Interfaces\Repository\Repository;

final class Handler implements DomainHandler
{
    /**
     * @var Repository
     */
    private $cardRepository;
    /**
     * @var Repository
     */
    private $cardLimitRepository;
    /**
     * @var Repository
     */
    private $fuelTypeRepository;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        Repository $cardRepository,
        Repository $cardLimitRepository,
        Repository $fuelTypeRepository,
        ObjectManager $objectManager
    ) {
        $this->cardRepository = $cardRepository;
        $this->cardLimitRepository = $cardLimitRepository;
        $this->objectManager = $objectManager;
        $this->fuelTypeRepository = $fuelTypeRepository;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        /** @var Card|null $entity */
        $entity = $this->cardRepository->find([
            'id_equalTo' => $handlerRequest->getId(),
            'status_equalTo' => CardStatus::active()->getValue(),
            ExportStatusCriteria::class => ExportStatus::canBeEditedStatuses(),
        ]);

        if (!$entity instanceof Card) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $startUseTime = new \DateTimeImmutable($handlerRequest->startUseTime);
        $endUseTime = new \DateTimeImmutable($handlerRequest->endUseTime);
        $entity->change(
            new MoneyLimits($handlerRequest->totalLimits['day'], $handlerRequest->totalLimits['week'], $handlerRequest->totalLimits['month']),
            new TimeUse($startUseTime, $endUseTime),
            ServiceSchedule::createByNames($handlerRequest->serviceDays)
        );
        $this->cardRepository->add($entity);

        $limits = $handlerRequest->getLimits();
        $fuelCodes = $this->createUpdateLimits($entity, $limits);
        $this->deleteLimits($entity, $fuelCodes);
        $this->objectManager->flush();

        /** @var FuelLimit[] $fuelLimits */
        $fuelLimits = $this->cardLimitRepository->findMany([
            'cardNumber_equalTo' => $entity->getCardNumber(),
            ExportStatusCriteria::class => ExportStatus::canBeEditedStatuses(),
        ]);

        foreach ($fuelLimits as $fuelLimit) {
            $fuelLimit->getExportStatus()->readyForExport();
            $this->cardLimitRepository->add($fuelLimit);
        }
        $this->objectManager->flush();

        return $entity;
    }

    private function createUpdateLimits(Card $card, array $limits): array
    {
        $fuelCodes = [];
        foreach ($limits as $limit) {
            /** @var Type $fuelType */
            $fuelType = $this->fuelTypeRepository->findById($limit['id']);

            if (in_array($fuelType->getFuelCode(), $fuelCodes)) {
                continue;
            }
            $fuelCodes[] = $fuelType->getFuelCode();

            $fuelLimit = $this->cardLimitRepository->find([
                'cardNumber_equalTo' => $card->getCardNumber(),
                'fuelCode_equalTo' => $fuelType->getFuelCode(),
                ExportStatusCriteria::class => ExportStatus::canBeEditedStatuses(),
            ]);

            if ($fuelLimit instanceof FuelLimit) {
                $fuelLimit->change(
                    $card,
                    $fuelType,
                    new Limits($limit['dayLimit'], $limit['weekLimit'], $limit['monthLimit']),
                    new \DateTimeImmutable()
                );
                $this->cardLimitRepository->add($fuelLimit);
                continue;
            }

            $newFuelLimit = FuelLimit::createFromForm(
                $card,
                $fuelType,
                new Limits($limit['dayLimit'], $limit['weekLimit'], $limit['monthLimit']),
                new \DateTimeImmutable()
            );
            $this->cardLimitRepository->add($newFuelLimit);
        }

        return $fuelCodes;
    }

    private function deleteLimits(Card $card, array $fuelCodes): void
    {
        /** @var FuelLimit[] $fuelLimits */
        $fuelLimits = $this->cardLimitRepository->findMany([
            'cardNumber_equalTo' => $card->getCardNumber(),
            'fuelCode_notIn' => $fuelCodes,
            ExportStatusCriteria::class => ExportStatus::canBeEditedStatuses(),
        ]);

        foreach ($fuelLimits as $fuelLimit) {
            $fuelLimit->delete($card, new \DateTimeImmutable());
            $this->cardLimitRepository->add($fuelLimit);
        }
    }
}
