<?php

namespace Tests\Unit\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Discount\Discount;
use App\Clients\Domain\Discount\ValueObject\DiscountId;
use App\Clients\Domain\Discount\ValueObject\DiscountSum;
use App\Import\Application\FileDataSaver\ClientDataSaver;
use App\Import\Application\FileDataSaver\DiscountDataSaver;
use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class DiscountDataSaverTest extends TestCase
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

        $this->serviceObject = new DiscountDataSaver($this->entityManagerMock->reveal(), $this->loggerMock->reveal(), $debug);
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(FileDataSaverInterface::class, $this->serviceObject);
    }

    public function testSupportFileMethodReturnTrue(): void
    {
        $this->assertEquals(true, $this->serviceObject->supportedFile('filename.dc'));
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
        $entity = self::createDiscount();

        $result = $this->serviceObject->getUniqueKeyFromEntity($entity);

        $dateString = $entity->getOperationDate()->format('Y-m-d H:i:s');

        $string = md5($entity->getClient1CId().$entity->getDiscountSum().$dateString);

        $this->assertEquals($string, $result);
    }

    public function testGetUniqueKeyFromRecordReturnString(): void
    {
        $record = [
            'unique',
            'unique2',
            'unique3',
        ];

        $result = $this->serviceObject->getUniqueKeyFromRecord($record);
        $this->assertEquals(md5($record[0].$record[1].$record[2]), $result);
    }

    public function testCreateEntityReturnEntity(): void
    {
        $array = [
            'КВ-0002179', '87899', '2019-03-26 23:59:59',
        ];

        $entity = self::createDiscount($array);

        $result = $this->serviceObject->createEntity($array);

        $this->assertEquals($entity->getClient1CId(), $result->getClient1CId());
    }

    public function testUpdateEntityReturnEntity(): void
    {
        $client1CId = 'КВ-0002179';
        $array = [
            $client1CId, '87899', '2019-03-26 23:59:59',
        ];

        $entity = self::createDiscount([$client1CId]);

        $result = $this->serviceObject->updateEntity($entity, $array);

        $this->assertNull($result);
    }

    private static function createDiscount(array $array = []): Discount
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = DiscountId::fromString($string);

        $record = array_merge([
            'КВ-0002179', '87899', '2019-03-26 23:59:59',
        ], $array);

        $client1CId = new Client1CId($record[0]);
        $discountSum = new DiscountSum($record[1]);
        $operationDate = new \DateTimeImmutable($record[2]);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return Discount::create(
            $identity,
            $client1CId,
            $discountSum,
            $operationDate,
            $dateTime
        );
    }
}
