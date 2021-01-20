<?php

namespace App\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Discount\Discount;
use App\Clients\Domain\Discount\ValueObject\DiscountId;
use App\Clients\Domain\Discount\ValueObject\DiscountSum;
use App\Import\Application\FileDataSaver\Writer\InsertUpdateWriter;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;

final class DiscountDataSaver extends InsertUpdateWriter implements FileDataSaverInterface
{
    private const FILE_EXTENSION = 'dc';

    public function supportedFile(string $fileName): bool
    {
        return self::FILE_EXTENSION === pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public function recordsChunkSize(): int
    {
        return 10000;
    }

    public function getUniqueKeyFromEntity($entity): ?string
    {
        if (!$entity instanceof Discount) {
            return null;
        }

        $dateString = $entity->getOperationDate()->format('Y-m-d H:i:s');

        return md5($entity->getClient1CId().$entity->getDiscountSum().$dateString);
    }

    public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery
    {
        $clientIds = [];
        $amounts = [];
        $dates = [];

        foreach ($items as $record) {
            $clientIds[] = $record[0];
            $amounts[] = $record[1];
            $dates[] = $record[2];
        }

        $query = $entityManager->createQuery(
            sprintf('
                        SELECT c FROM %s c 
                            WHERE c.client1CId IN (:clientIds) 
                            AND c.discountSum IN (:amounts) 
                            AND c.operationDate IN (:dates)
             ', Discount::class)
        );

        $query->setParameters([':clientIds' => $clientIds, ':amounts' => $amounts, ':dates' => $dates]);

        unset($clientIds);
        unset($amounts);
        unset($dates);

        return $query;
    }

    public function getUniqueKeyFromRecord(array $record): string
    {
        return md5($record[0].$record[1].$record[2]);
    }

    public function createEntity(array $record): object
    {
        $client1CId = new Client1CId($record[0]);
        $discountSum = new DiscountSum($record[1]);
        $operationDate = new \DateTimeImmutable($record[2]);

        $dateTime = new \DateTimeImmutable();

        return Discount::create(
            DiscountId::next(),
            $client1CId,
            $discountSum,
            $operationDate,
            $dateTime
        );
    }

    public function updateEntity($entity, array $record): void
    {
        $discountSum = new DiscountSum($record[1]);
        $operationDate = new \DateTimeImmutable($record[2]);

        $entity->update(
            $discountSum,
            $operationDate
        );
    }
}
