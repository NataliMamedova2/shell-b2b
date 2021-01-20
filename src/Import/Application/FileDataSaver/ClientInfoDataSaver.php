<?php

namespace App\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Domain\ClientInfo\ValueObject\Balance;
use App\Clients\Domain\ClientInfo\ValueObject\ClientPcId;
use App\Clients\Domain\ClientInfo\ValueObject\CreditLimit;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\ClientInfo\ValueObject\LastTransactionDate;
use App\Import\Application\FileDataSaver\Writer\InsertUpdateWriter;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;

final class ClientInfoDataSaver extends InsertUpdateWriter implements FileDataSaverInterface
{
    private const FILE_NAME = 'PIDCLi_R.txt';

    public function supportedFile(string $filename): bool
    {
        return self::FILE_NAME === $filename;
    }

    public function filterRecords(array $record): bool
    {
        return '&' === $record[0];
    }

    public function getUniqueKeyFromEntity($entity): ?string
    {
        if (!$entity instanceof ClientInfo) {
            return null;
        }

        return (string) $entity->getClientPcId();
    }

    public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery
    {
        $ids = [];
        foreach ($items as $record) {
            $ids[] = $this->getUniqueKeyFromRecord($record);
        }

        $query = $entityManager->createQuery(
            sprintf('SELECT c FROM %s c WHERE c.clientPcId IN (:ids)', ClientInfo::class)
        );
        $query->setParameters([':ids' => $ids]);
        unset($ids);

        return $query;
    }

    public function getUniqueKeyFromRecord(array $record): ?string
    {
        if (false === $this->isValid($record)) {
            return null;
        }

        $clientPcId = new ClientPcId($record[2]);

        return (string) $clientPcId->getValue();
    }

    public function createEntity(array $record): ?object
    {
        if (false === $this->isValid($record)) {
            return null;
        }

        $clientPcId = new ClientPcId($record[2]);
        $fcCbrId = new FcCbrId($record[3]);
        $balance = new Balance($record[4]);
        $lastTransactionDate = new LastTransactionDate(
            \DateTimeImmutable::createFromFormat('d/m/Y', $record[5]),
            new \DateTimeImmutable($record[6])
        );
        $creditLimit = new CreditLimit($record[7]);
        $dateTime = new \DateTimeImmutable();

        return ClientInfo::create(
            IdentityId::next(),
            $clientPcId,
            $fcCbrId,
            $balance,
            $creditLimit,
            $lastTransactionDate,
            $dateTime
        );
    }

    public function updateEntity($entity, array $record): void
    {
        if (false === $this->isValid($record)) {
            return;
        }

        $balance = new Balance($record[4]);
        $lastTransactionDate = new LastTransactionDate(
            \DateTimeImmutable::createFromFormat('d/m/Y', $record[5]),
            new \DateTimeImmutable($record[6])
        );
        $creditLimit = new CreditLimit($record[7]);
        $dateTime = new \DateTimeImmutable();

        $entity->update(
            $balance,
            $creditLimit,
            $lastTransactionDate,
            $dateTime
        );
    }

    private function isValid(array $record): bool
    {
        $record = array_diff($record, ['']);
        if (!isset($record[2]) || !isset($record[3]) || !isset($record[4]) || !isset($record[5]) || !isset($record[6]) || !isset($record[7])) {
            return false;
        }
        if (0 === intval($record[2]) || 0 === intval($record[3])) {
            return false;
        }

        return true;
    }
}
