<?php

namespace App\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\ValueObject\CardId;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Card\ValueObject\CarNumber;
use App\Clients\Domain\Card\ValueObject\DayLimit;
use App\Clients\Domain\Card\ValueObject\MonthLimit;
use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use App\Clients\Domain\Card\ValueObject\TimeUse;
use App\Clients\Domain\Card\ValueObject\WeekLimit;
use App\Import\Application\FileDataSaver\Writer\InsertUpdateWriter;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;

final class CardDataSaver extends InsertUpdateWriter implements FileDataSaverInterface
{
    private const FILE_EXTENSION = 'cr';

    public function supportedFile(string $fileName): bool
    {
        return self::FILE_EXTENSION === pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public function getUniqueKeyFromEntity($entity): ?string
    {
        if (!$entity instanceof Card) {
            return null;
        }

        return (string) $entity->getCardNumber();
    }

    public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery
    {
        $ids = [];
        foreach ($items as $record) {
            $ids[] = $record[1];
        }

        $query = $entityManager->createQuery(
            sprintf('SELECT c FROM %s c WHERE c.cardNumber IN (:ids)', Card::class)
        );
        $query->setParameters([':ids' => $ids]);
        unset($ids);

        return $query;
    }

    public function getUniqueKeyFromRecord(array $record): string
    {
        return (string) $record[1];
    }

    public function createEntity(array $record): ?object
    {
        $client1CId = $record[0];
        if (empty($client1CId)) {
            return null;
        }

        $serviceScheduleValue = !empty($record[6]) ? $record[6] : '0000000';

        $clientId = new Client1CId($client1CId);
        $cardNumber = new CardNumber($record[1]);
        $carNumber = new CarNumber($record[2]);
        $dayLimit = new DayLimit($record[3]);
        $weekLimit = new WeekLimit($record[4]);
        $monthLimit = new MonthLimit($record[5]);
        $serviceSchedule = new ServiceSchedule($serviceScheduleValue);
        $timeUse = new TimeUse(new \DateTimeImmutable($record[7]), new \DateTimeImmutable($record[8]));
        $cardStatus = new CardStatus($record[9]);

        $dateTime = new \DateTimeImmutable();

        return Card::create(
            CardId::next(),
            $clientId,
            $cardNumber,
            $carNumber,
            $dayLimit,
            $weekLimit,
            $monthLimit,
            $serviceSchedule,
            $timeUse,
            $cardStatus,
            $dateTime
        );
    }

    public function updateEntity($entity, array $record): void
    {
        $client1CId = $record[0];
        if (empty($client1CId)) {
            return;
        }

        $serviceScheduleValue = !empty($record[6]) ? $record[6] : '0000000';

        $clientId = new Client1CId($client1CId);
        $carNumber = new CarNumber($record[2]);
        $dayLimit = new DayLimit($record[3]);
        $weekLimit = new WeekLimit($record[4]);
        $monthLimit = new MonthLimit($record[5]);
        $serviceSchedule = new ServiceSchedule($serviceScheduleValue);
        $timeUse = new TimeUse(new \DateTimeImmutable($record[7]), new \DateTimeImmutable($record[8]));
        $cardStatus = new CardStatus($record[9]);

        $entity->update(
            $clientId,
            $carNumber,
            $dayLimit,
            $weekLimit,
            $monthLimit,
            $serviceSchedule,
            $timeUse,
            $cardStatus
        );
    }
}
