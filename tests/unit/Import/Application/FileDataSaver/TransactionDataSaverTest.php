<?php

namespace Tests\Unit\Import\Application\FileDataSaver;

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
use App\Import\Application\FileDataSaver\ClientDataSaver;
use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use App\Import\Application\FileDataSaver\TransactionDataSaver;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class TransactionDataSaverTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $entityManagerMock;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|LoggerInterface
     */
    private $loggerMock;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|Repository
     */
    private $repositoryMock;

    /**
     * @var ClientDataSaver
     */
    private $serviceObject;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->loggerMock = $this->prophesize(LoggerInterface::class);
        $this->repositoryMock = $this->prophesize(Repository::class);
        $debug = false;

        $this->serviceObject = new TransactionDataSaver($this->entityManagerMock->reveal(), $this->loggerMock->reveal(), $debug, $this->repositoryMock->reveal());
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(FileDataSaverInterface::class, $this->serviceObject);
    }

    public function testSupportFileMethodReturnTrue(): void
    {
        $this->assertEquals(true, $this->serviceObject->supportedFile('filename.tr'));
    }

    public function testSupportFileMethodReturnFalse(): void
    {
        $this->assertEquals(false, $this->serviceObject->supportedFile('filename.cc'));
    }

    public function testGetUniqueKeyFromEntityReturnNull(): void
    {
        $entity = new \stdClass();

        $result = $this->serviceObject->getUniqueKeyFromEntity($entity);
        $this->assertEquals(null, $result);
    }

    public function testGetUniqueKeyFromEntityReturnString(): void
    {
        $entity = self::createTransaction();

        $result = $this->serviceObject->getUniqueKeyFromEntity($entity);
        $this->assertEquals($entity->getTransaction1CId(), $result);
    }

    public function testGetUniqueKeyFromRecordReturnString(): void
    {
        $record = [
            'unique',
        ];

        $result = $this->serviceObject->getUniqueKeyFromRecord($record);
        $this->assertEquals($record[0], $result);
    }

    public function testCreateEntityReturnEntity(): void
    {
        $array = [
            'DF4D106456E01CA18443001BBFA6013A', 'КВЦ0007669', 2101031263, 'КВЦ0000008', 10000, 2929, 292900, 'КВЦ0000139', 'АЗС №R1106 Запорізька обл., м.Василівка, віул. Леніна, 2А',
            'КВЦ0000046', 'Запоріжжя АХУ', '2019-03-01 00:09:52', 0,
        ];

        $entity = self::createTransaction($array);

        $result = $this->serviceObject->createEntity($array);

        $this->assertEquals($entity->getTransaction1CId(), $result->getTransaction1CId());
        $this->assertEquals($entity->getCardNumber(), $result->getCardNumber());
    }

    public function testUpdateEntityReturnEntity(): void
    {
        $transactionId = 'КВ-DF4D106456E01CA18443001BBFA6013A';
        $array = [
            $transactionId, 'КВЦ0007669', 2101031263, 'КВЦ0000008', 10000, 2929, 292900, 'КВЦ0000139', 'АЗС №R1106 Запорізька обл., м.Василівка, віул. Леніна, 2А',
            'КВЦ0000046', 'Запоріжжя АХУ', '2019-03-01 00:09:52', 0,
        ];

        $entity = self::createTransaction([$transactionId]);

        $result = $this->serviceObject->updateEntity($entity, $array);

        $this->assertNull($result);
    }

    private static function createTransaction(array $array = []): Transaction
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = TransactionId::fromString($string);

        $record = array_merge([
            'DF4D106456E01CA18443001BBFA6013A', 'КВЦ0007669', 2101031263, 'КВЦ0000008', 10000, 2929, 292900, 'КВЦ0000139', 'АЗС №R1106 Запорізька обл., м.Василівка, віул. Леніна, 2А',
            'КВЦ0000046', 'Запоріжжя АХУ', '2019-03-01 00:09:52', 0,
        ], $array);

        $transaction1CId = new Transaction1CId($record[0]);
        $clientId = new Client1CId($record[1]);
        $cardNumber = new CardNumber($record[2]);
        $fuelCode = new FuelCode($record[3]);
        $fuelQuantity = new FuelQuantity($record[4]);
        $stellaPrice = new StellaPrice($record[5]);
        $debit = new Debit($record[6]);
        $azsCode = new AzcCode($record[7]);
        $azsName = new AzcName($record[8]);
        $regionCode = new RegionCode($record[9]);
        $regionName = new RegionName($record[10]);
        $postDate = new \DateTimeImmutable($record[11]);
        $type = new Type($record[12]);
        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return Transaction::create(
            $identity,
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
}
