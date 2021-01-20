<?php

namespace App\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Client\ValueObject\Agent1CId;
use App\Clients\Domain\Client\ValueObject\ClientId;
use App\Clients\Domain\Client\ValueObject\ContractNumber;
use App\Clients\Domain\Client\ValueObject\EdrpouInn;
use App\Clients\Domain\Client\ValueObject\FullName;
use App\Clients\Domain\Client\ValueObject\Manager1CId;
use App\Clients\Domain\Client\ValueObject\NktId;
use App\Clients\Domain\Client\ValueObject\Status;
use App\Clients\Domain\Client\ValueObject\Type;
use App\Import\Application\FileDataSaver\Writer\InsertUpdateWriter;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;

final class ClientDataSaver extends InsertUpdateWriter implements FileDataSaverInterface
{
    private const FILE_EXTENSION = 'cl';

    public function supportedFile(string $fileName): bool
    {
        return self::FILE_EXTENSION === pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public function getUniqueKeyFromEntity($entity): ?string
    {
        if (!$entity instanceof Client) {
            return null;
        }

        return (string) $entity->getClient1CId();
    }

    public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery
    {
        $ids = [];
        foreach ($items as $record) {
            $ids[] = $record[0];
        }

        $query = $entityManager->createQuery(
            sprintf('SELECT c FROM %s c WHERE c.client1CId IN (:ids)', Client::class)
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
        $fullName = new FullName($record[1]);
        $edrpouInn = new EdrpouInn($record[2]);
        $type = new Type($record[3]);
        $nktId = new NktId((int) $record[4]);
        $managerId = new Manager1CId($record[5]);
        $agentId = new Agent1CId($record[6]);
        $fcCbrId = new FcCbrId($record[7]);
        $status = new Status($record[8]);
        $contractNumber = new ContractNumber($record[9]);
        $dateTime = $record[10] ?? 'now';
        $contractDate = new \DateTimeImmutable($dateTime);
        $dateTime = new \DateTimeImmutable();

        return Client::createWithContract(
            ClientId::next(),
            $clientId,
            $fullName,
            $edrpouInn,
            $type,
            $nktId,
            $managerId,
            $agentId,
            $fcCbrId,
            $status,
            $contractNumber,
            $contractDate,
            $dateTime
        );
    }

    public function updateEntity($entity, array $record): void
    {
        $fullName = new FullName($record[1]);
        $edrpouInn = new EdrpouInn($record[2]);
        $type = new Type($record[3]);
        $nktId = new NktId((int) $record[4]);
        $managerId = new Manager1CId($record[5]);
        $agentId = new Agent1CId($record[6]);
        $fcCbrId = new FcCbrId($record[7]);
        $status = new Status($record[8]);
        $contractNumber = new ContractNumber($record[9]);
        $dateTime = $record[10] ?? 'now';
        $contractDate = new \DateTimeImmutable($dateTime);

        $entity->updateWithContract(
            $fullName,
            $edrpouInn,
            $type,
            $nktId,
            $managerId,
            $agentId,
            $fcCbrId,
            $status,
            $contractNumber,
            $contractDate
        );
    }
}
