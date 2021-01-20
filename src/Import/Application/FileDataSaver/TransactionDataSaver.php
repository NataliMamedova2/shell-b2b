<?php

namespace App\Import\Application\FileDataSaver;

use App\Clients\Domain\Fuel\Type\ReplacementFuelType;
use App\Clients\Domain\Fuel\Type\ValueObject\RepacementTypeId;
use App\Clients\Domain\Transaction\Card\Transaction;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcCode;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcName;
use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Transaction\Card\ValueObject\Debit;
use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Transaction\Card\ValueObject\FuelQuantity;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionCode;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionName;
use App\Clients\Domain\Transaction\Card\ValueObject\StellaPrice;
use App\Clients\Domain\Transaction\Card\ValueObject\Transaction1CId;
use App\Clients\Domain\Transaction\Card\ValueObject\TransactionId;
use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Import\Application\FileDataSaver\Writer\InsertUpdateWriter;
use Infrastructure\Interfaces\Repository\Repository;
use Psr\Log\LoggerInterface;

final class TransactionDataSaver extends InsertUpdateWriter implements FileDataSaverInterface
{
    private const FILE_EXTENSION = 'tr';

    /**
     * @var Repository
     */
    private $replacementFuelTypeRepository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, bool $debug = false, Repository $replacementFuelTypeRepository)
    {
        parent::__construct($entityManager, $logger, $debug);

        $this->replacementFuelTypeRepository = $replacementFuelTypeRepository;
    }


    public function supportedFile(string $fileName): bool
    {
        return self::FILE_EXTENSION === pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public function getUniqueKeyFromEntity($entity): ?string
    {
        if (!$entity instanceof Transaction) {
            return null;
        }

        return (string) $entity->getTransaction1CId();
    }

    public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery
    {
        $ids = [];
        foreach ($items as $record) {
            $ids[] = $record[0];
        }

        $query = $entityManager->createQuery(
            sprintf('SELECT c FROM %s c WHERE c.transactionId IN (:ids)', Transaction::class)
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
        $fuelCodeString = $this->checkForReplacements($record[3]);

        $transaction1CId = new Transaction1CId($record[0]);
        $clientId = new Client1CId($record[1]);
        $cardNumber = new CardNumber($record[2]);
        $fuelCode = new FuelCode($fuelCodeString);
        $fuelQuantity = new FuelQuantity($record[4]);
        $stellaPrice = new StellaPrice($record[5]);
        $debit = new Debit($record[6]);
        $azsCode = new AzcCode($record[7]);
        $azsName = new AzcName($record[8]);
        $regionCode = new RegionCode($record[9]);
        $regionName = new RegionName($record[10]);
        $postDate = new \DateTimeImmutable($record[11]);
        $type = new Type($record[12]);
        $dateTime = new \DateTimeImmutable();

        return Transaction::create(
            TransactionId::next(),
            $transaction1CId,
            $clientId,
            $cardNumber,
            $fuelCode,
            $fuelQuantity,
            $stellaPrice,
            $debit,
            $azsCode,
            $azsName,
            $regionCode,
            $regionName,
            $postDate,
            $type,
            $dateTime
        );
    }

    public function updateEntity($entity, array $record): void
    {
        $fuelCodeString = $this->checkForReplacements($record[3]);

        $clientId = new Client1CId($record[1]);
        $cardNumber = new CardNumber($record[2]);
        $fuelCode = new FuelCode($fuelCodeString);
        $fuelQuantity = new FuelQuantity($record[4]);
        $stellaPrice = new StellaPrice($record[5]);
        $debit = new Debit($record[6]);
        $azsCode = new AzcCode($record[7]);
        $azsName = new AzcName($record[8]);
        $regionCode = new RegionCode($record[9]);
        $regionName = new RegionName($record[10]);
        $postDate = new \DateTimeImmutable($record[11]);
        $type = new Type($record[12]);

        $entity->update(
            $clientId,
            $cardNumber,
            $fuelCode,
            $fuelQuantity,
            $stellaPrice,
            $debit,
            $azsCode,
            $azsName,
            $regionCode,
            $regionName,
            $postDate,
            $type
        );
    }


    public function checkForReplacements(string $code): string
    {
        /** @var ?ReplacementFuelType $replacementFuelType */
        $replacementFuelType = $this->replacementFuelTypeRepository->find([
            'fuelCode_equalTo' => $code,
        ]);

        if (isset($replacementFuelType)) {
            return $replacementFuelType->getFuelReplacementCode();
        }
        return $code;
    }
}
