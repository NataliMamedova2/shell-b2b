<?php

namespace App\Import\Application\FileDataSaver;

use App\Clients\Domain\RefillBalance\RefillBalance;
use App\Clients\Domain\RefillBalance\ValueObject\Amount;
use App\Clients\Domain\RefillBalance\ValueObject\CardOwner;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\RefillBalance\ValueObject\Operation;
use App\Clients\Domain\RefillBalance\ValueObject\OperationDate;
use App\Clients\Domain\RefillBalance\ValueObject\RefillBalanceId;
use App\Import\Application\FileDataSaver\Writer\InsertUpdateWriter;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;

final class RefillBalanceDataSaver extends InsertUpdateWriter implements FileDataSaverInterface
{
    private const FILE_EXTENSION = 'tc';
    private const CHUNK_SIZE = 10000;

    public function supportedFile(string $fileName): bool
    {
        return self::FILE_EXTENSION === pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public function recordsChunkSize(): int
    {
        return self::CHUNK_SIZE;
    }

    /**
     * @param RefillBalance $entity
     *
     * @return string|null
     */
    public function getUniqueKeyFromEntity($entity): ?string
    {
        if (!$entity instanceof RefillBalance) {
            return null;
        }

        $cardOwner = new CardOwner($entity->getCardOwner());
        $fcCbrId = new FcCbrId($entity->getFcCbrId());
        $operation = new Operation($entity->getOperation());
        $amount = new Amount($entity->getAmount());
        $date = $entity->getOperationDateTime();

        $data = [
            $cardOwner->getValue(),
            $fcCbrId->getValue(),
            $operation->getValue(),
            $amount->getValue(),
            $date->format('c'),
        ];

        return md5(implode('', $data));
    }

    public function getUniqueKeyFromRecord(array $record): string
    {
        $cardOwner = new CardOwner((int)$record[0]);
        $fcCbrId = new FcCbrId($record[1]);
        $operation = new Operation((int)$record[3]);
        $amount = new Amount($record[4]);
        $operationDate = \DateTimeImmutable::createFromFormat('d/m/y', $record[5]);
        $operationTime = new \DateTimeImmutable($record[6]);
        $date = new OperationDate($operationDate, $operationTime);

        $data = [
            $cardOwner->getValue(),
            $fcCbrId->getValue(),
            $operation->getValue(),
            $amount->getValue(),
            $date->getValue()->format('c'),
        ];

        return md5(implode('', $data));
    }

    public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery
    {
        $cardOwner = new \ArrayIterator();
        $fcCbrId = new \ArrayIterator();
        $operation = new \ArrayIterator();
        $amount = new \ArrayIterator();
        $date = new \ArrayIterator();
        foreach ($items as $record) {
            $cardOwnerValue = new CardOwner((int)$record[0]);
            $cardOwner->append($cardOwnerValue->getValue());

            $fcCbrIdValue = new FcCbrId($record[1]);
            $fcCbrId->append($fcCbrIdValue->getValue());

            $operationValue = new Operation((int)$record[3]);
            $operation->append($operationValue->getValue());

            $amountValue = new Amount($record[4]);
            $amount->append($amountValue->getValue());

            $operationDate = \DateTimeImmutable::createFromFormat('d/m/y', $record[5]);
            $operationTime = new \DateTimeImmutable($record[6]);
            $dateTime = new OperationDate($operationDate, $operationTime);
            $date->append($dateTime->getValue()->format("Y-m-d H:i:s"));
        }
        $query = $entityManager->createQuery(
            sprintf('SELECT c FROM %s c WHERE 
                         c.cardOwner IN (:cardOwner)
                         AND c.fcCbrId IN (:fcCbrId)
                         AND c.operation IN (:operation)
                         AND c.amount IN (:amount)
                         AND c.operationDate IN (:date)
                         ', RefillBalance::class)
        );

        $query->setParameters([
            'cardOwner' => $cardOwner->getArrayCopy(),
            'fcCbrId' => $fcCbrId->getArrayCopy(),
            'operation' => $operation->getArrayCopy(),
            'amount' => $amount->getArrayCopy(),
            'date' => $date->getArrayCopy(),
        ]);

        return $query;
    }

    public function createEntity(array $record): object
    {
        $identity = RefillBalanceId::next();
        $cardOwner = new CardOwner((int)$record[0]);
        $fcCbrId = new FcCbrId($record[1]);
        $operation = new Operation((int)$record[3]);
        $amount = new Amount($record[4]);
        $operationDate = \DateTimeImmutable::createFromFormat('d/m/y', $record[5]);
        $operationTime = new \DateTimeImmutable($record[6]);
        $dateTime = new OperationDate($operationDate, $operationTime);

        return new RefillBalance(
            $identity,
            $cardOwner,
            $fcCbrId,
            $operation,
            $amount,
            $dateTime
        );
    }

    public function updateEntity($entity, array $record): void
    {
        $cardOwner = new CardOwner((int)$record[0]);
        $fcCbrId = new FcCbrId($record[1]);
        $operation = new Operation((int)$record[3]);
        $amount = new Amount($record[4]);
        $operationDate = \DateTimeImmutable::createFromFormat('d/m/y', $record[5]);
        $operationTime = new \DateTimeImmutable($record[6]);
        $dateTime = new OperationDate($operationDate, $operationTime);

        $entity->update(
            $cardOwner,
            $fcCbrId,
            $operation,
            $amount,
            $dateTime
        );
    }
}
