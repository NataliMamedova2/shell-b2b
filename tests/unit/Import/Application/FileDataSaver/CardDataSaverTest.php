<?php

namespace Tests\Unit\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\ValueObject\CardId;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Card\ValueObject\CarNumber;
use App\Clients\Domain\Card\ValueObject\DayLimit;
use App\Clients\Domain\Card\ValueObject\MonthLimit;
use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use App\Clients\Domain\Card\ValueObject\TimeUse;
use App\Clients\Domain\Card\ValueObject\WeekLimit;
use App\Import\Application\FileDataSaver\CardDataSaver;
use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class CardDataSaverTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $entityManagerMock;

    /**
     * @var CardDataSaver
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

        $this->serviceObject = new CardDataSaver($this->entityManagerMock->reveal(), $this->loggerMock->reveal(), $debug);
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(FileDataSaverInterface::class, $this->serviceObject);
    }

    public function testSupportFileMethodReturnTrue(): void
    {
        $this->assertEquals(true, $this->serviceObject->supportedFile('filename.cr'));
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
        $entity = self::createCard();

        $result = $this->serviceObject->getUniqueKeyFromEntity($entity);
        $this->assertEquals($entity->getCardNumber(), $result);
    }

    public function testGetUniqueKeyFromRecordReturnString(): void
    {
        $record = [
            'qwewqeq',
            'unique',
        ];

        $result = $this->serviceObject->getUniqueKeyFromRecord($record);
        $this->assertEquals($record[1], $result);
    }

    public function testCreateEntityReturnEntity(): void
    {
        $array = [
            'КВ-0004644', '2101059078', 'АІ 8568 ЕВ', '10000000', '70000000', '300000000', '1111111', '00:00:00', '23:59:59', 1,
        ];

        $entity = self::createCard($array);

        $result = $this->serviceObject->createEntity($array);

        $this->assertEquals($entity->getCardNumber(), $result->getCardNumber());
        $this->assertEquals($entity->getCardNumber(), $result->getCardNumber());
    }

    public function testUpdateEntityReturnEntity(): void
    {
        $cardNumber = '2101059078';
        $array = [
            'КВ-0004644', $cardNumber, 'АІ 8568 ЕВ', '10000000', '70000000', '300000000', '1111111', '00:00:00', '23:59:59', 1,
        ];

        $entity = self::createCard([$cardNumber]);

        $result = $this->serviceObject->updateEntity($entity, $array);

        $this->assertNull($result);
    }

    private static function createCard(array $array = []): Card
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = CardId::fromString($string);

        $record = array_merge([
            'КВ-0004644', '2101059078', 'АІ 8568 ЕВ', '10000000', '70000000', '300000000', '1111111', '00:00:00', '23:59:59', 1,
        ], $array);

        $clientId = new Client1CId($record[0]);
        $cardNumber = new CardNumber($record[1]);
        $carNumber = new CarNumber($record[2]);
        $dayLimit = new DayLimit($record[3]);
        $weekLimit = new WeekLimit($record[4]);
        $monthLimit = new MonthLimit($record[5]);
        $serviceSchedule = new ServiceSchedule($record[6]);
        $timeUse = new TimeUse(new \DateTimeImmutable($record[7]), new \DateTimeImmutable($record[8]));
        $cardStatus = new CardStatus($record[9]);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return Card::create(
            $identity,
            $clientId,
            $cardNumber,
            $carNumber,
            $dayLimit,
            $weekLimit,
            $monthLimit,
            $serviceSchedule,
            $timeUse,
            $cardStatus,
            $dateTime
        );
    }
}
