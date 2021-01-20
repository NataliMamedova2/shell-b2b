<?php

namespace App\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\FuelLimit\FuelLimit;
use App\Clients\Domain\FuelLimit\ValueObject\DayLimit;
use App\Clients\Domain\FuelLimit\ValueObject\FuelId;
use App\Clients\Domain\FuelLimit\ValueObject\MonthLimit;
use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use App\Clients\Domain\FuelLimit\ValueObject\WeekLimit;
use App\Import\Application\FileDataSaver\Writer\InsertUpdateWriter;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;

final class FuelCardLimitDataSaver extends InsertUpdateWriter implements FileDataSaverInterface
{
    private const FILE_EXTENSION = 'fl';

    public function supportedFile(string $fileName): bool
    {
        return self::FILE_EXTENSION === pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public function recordsChunkSize(): int
    {
        return 10000;
    }

    public function getUniqueKeyFromRecord(array $record): string
    {
        return md5($record[1].$record[2]);
    }

    public function getUniqueKeyFromEntity($entity): ?string
    {
        if (!$entity instanceof FuelLimit) {
            return null;
        }

        return md5($entity->getCardNumber().$entity->getFuelCode());
    }

    public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery
    {
        $cardNumbers = [];
        $fuelCodes = [];
        foreach ($items as $record) {
            $cardNumber = $record[1];
            if (!in_array($cardNumber, $cardNumbers)) {
                $cardNumbers[] = $cardNumber;
            }
            $fuelCode = $record[2];
            if (!in_array($fuelCode, $fuelCodes)) {
                $fuelCodes[] = $fuelCode;
            }
        }

        $query = $entityManager->createQuery(
            sprintf('SELECT c FROM %s c WHERE c.cardNumber IN (:cardNumbers) and c.fuelCode IN (:fuelCodes)', FuelLimit::class)
        );

        $query->setParameters([':cardNumbers' => $cardNumbers, ':fuelCodes' => $fuelCodes]);

        unset($cardNumbers);
        unset($fuelCodes);

        return $query;
    }

    public function createEntity(array $record): ?object
    {
        $client1CId = $record[0];
        if (empty($client1CId)) {
            return null;
        }
        $clientId = new Client1CId($record[0]);
        $cardNumber = new CardNumber($record[1]);
        $fuelCode = new FuelCode($record[2]);
        $dayLimit = new DayLimit($record[3]);
        $weekLimit = new WeekLimit($record[4]);
        $monthLimit = new MonthLimit($record[5]);
        $purseActivity = new PurseActivity($record[6]);

        $dateTime = new \DateTimeImmutable();

        return FuelLimit::create(
            FuelId::next(),
            $clientId,
            $cardNumber,
            $fuelCode,
            $dayLimit,
            $weekLimit,
            $monthLimit,
            $purseActivity,
            $dateTime
        );
    }

    public function updateEntity($entity, array $record): void
    {
        $client1CId = $record[0];
        if (empty($client1CId)) {
            return;
        }

        $clientId = new Client1CId($record[0]);
        $fuelCode = new FuelCode($record[2]);
        $dayLimit = new DayLimit($record[3]);
        $weekLimit = new WeekLimit($record[4]);
        $monthLimit = new MonthLimit($record[5]);
        $purseActivity = new PurseActivity($record[6]);

        $entity->update(
            $clientId,
            $fuelCode,
            $dayLimit,
            $weekLimit,
            $monthLimit,
            $purseActivity
        );
    }
}
