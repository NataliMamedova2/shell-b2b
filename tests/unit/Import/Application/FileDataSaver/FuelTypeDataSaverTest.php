<?php

namespace Tests\Unit\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelName;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelPurse;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\Fuel\Type\ValueObject\PurseCode;
use App\Clients\Domain\Fuel\Type\ValueObject\TypeId;
use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use App\Import\Application\FileDataSaver\FuelTypeDataSaver;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class FuelTypeDataSaverTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $entityManagerMock;

    /**
     * @var FuelTypeDataSaver
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

        $this->serviceObject = new FuelTypeDataSaver($this->entityManagerMock->reveal(), $this->loggerMock->reveal(), $debug);
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(FileDataSaverInterface::class, $this->serviceObject);
    }

    public function testSupportFileMethodReturnTrue(): void
    {
        $this->assertEquals(true, $this->serviceObject->supportedFile('filename.ft'));
    }

    public function testSupportFileMethodReturnFalse(): void
    {
        $this->assertEquals(false, $this->serviceObject->supportedFile('filename.cc'));
    }

    public function testGetUniqueKeyFromRecordReturnString(): void
    {
        $record = [
            'КВ-00000004', 'Омыватель  SMART 0 С 4л', 1, 2, 40,
        ];

        $result = $this->serviceObject->getUniqueKeyFromRecord($record);
        $this->assertEquals(null, $result);
    }

    public function testCreateEntityReturnEntity(): void
    {
        $array = [
            'КВ-00000004', 'Омыватель  SMART 0 С 4л', 1, 2, 40,
        ];

        $entity = self::createFuelType($array);

        $result = $this->serviceObject->createEntity($array);

        $this->assertEquals($entity->getFuelCode(), $result->getFuelCode());
    }

    private static function createFuelType(array $array = []): Type
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = TypeId::fromString($string);

        $record = array_merge([
            'КВ-00000004', 'Омыватель  SMART 0 С 4л', 1, 2, 40,
        ], $array);

        $fuelCode = new FuelCode($record[0]);
        $fuelName = new FuelName($record[1]);
        $fuelPurse = new FuelPurse($record[2]);
        $fuelType = new FuelType($record[3]);
        $additionalType = new PurseCode($record[4]);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return Type::create(
            $identity,
            $fuelCode,
            $fuelName,
            $fuelPurse,
            $fuelType,
            $additionalType,
            $dateTime
        );
    }
}
