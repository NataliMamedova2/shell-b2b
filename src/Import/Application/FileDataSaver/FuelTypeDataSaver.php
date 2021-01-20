<?php

namespace App\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Fuel\Type\ValueObject\PurseCode;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelName;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelPurse;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\Fuel\Type\ValueObject\TypeId;
use App\Import\Application\FileDataSaver\Writer\InsertDeleteWriter;

final class FuelTypeDataSaver extends InsertDeleteWriter implements FileDataSaverInterface
{
    private const FILE_EXTENSION = 'ft';

    public function supportedFile(string $fileName): bool
    {
        return self::FILE_EXTENSION === pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public function createEntity(array $record): object
    {
        $fuelCode = new FuelCode($record[0]);
        $fuelName = new FuelName($record[1]);
        $fuelPurse = new FuelPurse((bool) $record[2]);
        $fuelType = new FuelType((int) $record[3]);
        $purseCode = new PurseCode((int) $record[4]);

        $dateTime = new \DateTimeImmutable();

        return Type::create(
            TypeId::next(),
            $fuelCode,
            $fuelName,
            $fuelPurse,
            $fuelType,
            $purseCode,
            $dateTime
        );
    }

    public function delete(\ArrayIterator $record): void
    {
        $em = $this->entityManager;

        $classMetaData = $em->getClassMetadata(Type::class);
        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $q = $dbPlatform->getTruncateTableSql($classMetaData->getTableName());
            $connection->executeUpdate($q);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
    }
}
