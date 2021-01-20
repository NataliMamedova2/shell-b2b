<?php

namespace App\Import\Application\FileDataSaver;

use App\Clients\Domain\Client\Contract;
use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Client\ValueObject\ContractId;
use App\Clients\Domain\Client\ValueObject\DsgCaGhb;
use App\Clients\Domain\Client\ValueObject\EckDsgCa;
use App\Clients\Domain\Client\ValueObject\FixedSum;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Import\Application\FileDataSaver\Writer\InsertUpdateWriter;

final class ClientContractDataSaver extends InsertUpdateWriter implements FileDataSaverInterface
{
    private const FILE_EXTENSION = 'cc';

    public function supportedFile(string $fileName): bool
    {
        return self::FILE_EXTENSION === pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public function getUniqueKeyFromEntity($entity): ?string
    {
        return (string) $entity->getClient1CId();
    }

    public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery
    {
        $ids = [];
        foreach ($items as $record) {
            $ids[] = $record[0];
        }

        $query = $entityManager->createQuery(
            sprintf('SELECT c FROM %s c WHERE c.client1CId IN (:ids)', Contract::class)
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
        $clientId = new Client1CId($record[0]);
        $eckDsgCa = new EckDsgCa($record[1]);
        $dsgCaGhb = new DsgCaGhb($record[2]);
        $fixedSum = new FixedSum($record[3]);
        $dateTime = new \DateTimeImmutable();

        return Contract::create(
            ContractId::next(),
            $clientId,
            $eckDsgCa,
            $dsgCaGhb,
            $fixedSum,
            $dateTime
        );
    }

    public function updateEntity($entity, array $record): void
    {
        $eckDsgCa = new EckDsgCa($record[1]);
        $dsgCaGhb = new DsgCaGhb($record[2]);
        $fixedSum = new FixedSum($record[3]);

        $entity->update(
            $eckDsgCa,
            $dsgCaGhb,
            $fixedSum
        );
    }
}
