<?php

namespace App\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Price\Price;
use App\Clients\Domain\Fuel\Price\ValueObject\FuelPrice;
use App\Clients\Domain\Fuel\Price\ValueObject\PriceId;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Import\Application\FileDataSaver\Writer\InsertUpdateWriter;

final class FuelPriceDataSaver extends InsertUpdateWriter implements FileDataSaverInterface
{
    private const FILE_EXTENSION = 'pf';

    public function supportedFile(string $fileName): bool
    {
        return self::FILE_EXTENSION === pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public function getUniqueKeyFromEntity($entity): ?string
    {
        if (!$entity instanceof Price) {
            return null;
        }

        return (string) $entity->getFuelCode();
    }

    public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery
    {
        $ids = [];
        foreach ($items as $record) {
            $ids[] = $record[0];
        }

        $query = $entityManager->createQuery(
            sprintf('SELECT c FROM %s c WHERE c.fuelCode IN (:ids)', Price::class)
        );
        $query->setParameters([':ids' => $ids]);
        unset($ids);

        return $query;
    }

    public function getUniqueKeyFromRecord(array $record): string
    {
        return (string) $record[0];
    }

    public function createEntity(array $record): object
    {
        $fuelCode = new FuelCode($record[0]);
        $fuelPrice = new FuelPrice($record[1]);

        $dateTime = new \DateTimeImmutable();

        $entity = Price::create(
            PriceId::next(),
            $fuelCode,
            $fuelPrice,
            $dateTime
        );

        return $entity;
    }

    public function updateEntity($entity, array $record): void
    {
        $fuelPrice = new FuelPrice($record[1]);

        $entity->update(
            $fuelPrice
        );
    }
}
