<?php

namespace Tests\Unit\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Price\Price;
use App\Clients\Domain\Fuel\Price\ValueObject\FuelPrice;
use App\Clients\Domain\Fuel\Price\ValueObject\PriceId;
use App\Import\Application\FileDataSaver\ClientDataSaver;
use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use App\Import\Application\FileDataSaver\FuelPriceDataSaver;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class FuelPriceDataSaverTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $entityManagerMock;

    /**
     * @var ClientDataSaver
     */
    private $serviceObject;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|LoggerInterface
     */
    private $loggerMock;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->loggerMock = $this->prophesize(LoggerInterface::class);
        $debug = false;

        $this->serviceObject = new FuelPriceDataSaver($this->entityManagerMock->reveal(), $this->loggerMock->reveal(), $debug);
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(FileDataSaverInterface::class, $this->serviceObject);
    }

    public function testSupportFileMethodReturnTrue(): void
    {
        $this->assertEquals(true, $this->serviceObject->supportedFile('filename.pf'));
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
        $entity = self::createFuelPrice();

        $result = $this->serviceObject->getUniqueKeyFromEntity($entity);
        $this->assertEquals($entity->getFuelCode(), $result);
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
            'КВЦ0000004', 2149,
        ];

        $entity = self::createFuelPrice($array);

        $result = $this->serviceObject->createEntity($array);

        $this->assertEquals($entity->getFuelCode(), $result->getFuelCode());
    }

    public function testUpdateEntityReturnEntity(): void
    {
        $fuelCode = 'КВЦ0000004';
        $array = [
            $fuelCode, 2149,
        ];

        $entity = self::createFuelPrice([$fuelCode]);

        $result = $this->serviceObject->updateEntity($entity, $array);

        $this->assertNull($result);
    }

    private static function createFuelPrice(array $array = []): Price
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = PriceId::fromString($string);

        $record = array_merge([
            'КВЦ0000004', 2149,
        ], $array);

        $fuelCode = new FuelCode($record[0]);
        $fuelPrice = new FuelPrice($record[1]);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return Price::create(
            $identity,
            $fuelCode,
            $fuelPrice,
            $dateTime
        );
    }
}
