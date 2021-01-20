<?php

namespace Tests\Unit\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\FuelLimit\FuelLimit;
use App\Clients\Domain\FuelLimit\ValueObject\DayLimit;
use App\Clients\Domain\FuelLimit\ValueObject\FuelId;
use App\Clients\Domain\FuelLimit\ValueObject\MonthLimit;
use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use App\Clients\Domain\FuelLimit\ValueObject\WeekLimit;
use App\Import\Application\FileDataSaver\ClientDataSaver;
use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use App\Import\Application\FileDataSaver\FuelCardLimitDataSaver;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class FuelCardLimitDataSaverTest extends TestCase
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

        $this->serviceObject = new FuelCardLimitDataSaver($this->entityManagerMock->reveal(), $this->loggerMock->reveal(), $debug);
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(FileDataSaverInterface::class, $this->serviceObject);
    }

    public function testSupportFileMethodReturnTrue(): void
    {
        $this->assertEquals(true, $this->serviceObject->supportedFile('filename.fl'));
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
        $entity = self::createFuelCard();

        $result = $this->serviceObject->getUniqueKeyFromEntity($entity);

        $string = md5($entity->getCardNumber().$entity->getFuelCode());
        $this->assertEquals($string, $result);
    }

    public function testGetUniqueKeyFromRecordReturnString(): void
    {
        $record = [
            'client-id',
            'card-number',
            'fuel-code',
        ];

        $result = $this->serviceObject->getUniqueKeyFromRecord($record);

        $string = md5($record[1].$record[2]);
        $this->assertEquals($string, $result);
    }

    public function testCreateEntityReturnEntity(): void
    {
        $array = [
            'ТКЦ0000007', '2001000421', 'СКЦОООООО2', 500, 1700, 3500, 1,
        ];

        $result = $this->serviceObject->createEntity($array);

        $this->assertEquals($array[0], $result->getClient1CId());
        $this->assertEquals($array[1], $result->getCardNumber());
        $this->assertEquals($array[2], $result->getFuelCode());
    }

    public function testUpdateEntityReturnEntity(): void
    {
        $clientId = 'ТКЦ0000007';
        $cardNumber = '2001000421';
        $fuelCode = '2001000421';
        $array = [
            $clientId, $cardNumber, $fuelCode, 500, 1700, 3500, 1,
        ];

        $entity = self::createFuelCard([$cardNumber]);

        $result = $this->serviceObject->updateEntity($entity, $array);

        $this->assertNull($result);
    }

    private static function createFuelCard(array $array = []): FuelLimit
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = FuelId::fromString($string);

        $record = array_merge([
            'ТКЦ0000007', '2001000421', 'СКЦОООООО2', 500, 1700, 3500, 1,
        ], $array);

        $clientId = new Client1CId($record[0]);
        $cardNumber = new CardNumber($record[1]);
        $fuelCode = new FuelCode($record[2]);
        $dayLimit = new DayLimit($record[3]);
        $weekLimit = new WeekLimit($record[4]);
        $monthLimit = new MonthLimit($record[5]);
        $purseActivity = new PurseActivity($record[6]);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return FuelLimit::create(
            $identity,
            $clientId,
            $cardNumber,
            $fuelCode,
            $dayLimit,
            $weekLimit,
            $monthLimit,
            $purseActivity,
            $dateTime
        );
    }
}
