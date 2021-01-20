<?php

namespace Tests\Unit\Import\Application\FileDataSaver;

use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Client\ValueObject\Agent1CId;
use App\Clients\Domain\Client\ValueObject\ClientId;
use App\Clients\Domain\Client\ValueObject\ContractNumber;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\Client\ValueObject\EdrpouInn;
use App\Clients\Domain\Client\ValueObject\FullName;
use App\Clients\Domain\Client\ValueObject\Manager1CId;
use App\Clients\Domain\Client\ValueObject\NktId;
use App\Clients\Domain\Client\ValueObject\Status;
use App\Clients\Domain\Client\ValueObject\Type;
use App\Import\Application\FileDataSaver\ClientDataSaver;
use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ClientDataSaverTest extends TestCase
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
     * @var ClientDataSaver
     */
    private $serviceObject;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->loggerMock = $this->prophesize(LoggerInterface::class);
        $debug = false;

        $this->serviceObject = new ClientDataSaver($this->entityManagerMock->reveal(), $this->loggerMock->reveal(), $debug);
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(FileDataSaverInterface::class, $this->serviceObject);
    }

    public function testSupportFileMethodReturnTrue(): void
    {
        $this->assertEquals(true, $this->serviceObject->supportedFile('filename.cl'));
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
        $entity = self::createClient();

        $result = $this->serviceObject->getUniqueKeyFromEntity($entity);
        $this->assertEquals($entity->getClient1CId(), $result);
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
        $array = ['КВ-0004888', 'Лавров Олександр Генадійович', '24584810', 0, 9180004888, 'КВЦ0000161', '', 12435, 1, 'КРБК-010103', '2014-04-08'];

        $entity = self::createClient($array);

        $result = $this->serviceObject->createEntity($array);

        $this->assertEquals($entity->getClient1CId(), $result->getClient1CId());
        $this->assertEquals($entity->getFullName(), $result->getFullName());
    }

    public function testUpdateEntityReturnEntity(): void
    {
        $clientId = 'КВ-0004888';
        $array = [$clientId, 'Лавров Олександр Генадійович2', '24584810', 1, 9180004882, 'КВЦ0000162', '', 55435, 0, 'КРБК-010103', '2014-04-08'];

        $entity = self::createClient([$clientId]);

        $result = $this->serviceObject->updateEntity($entity, $array);

        $this->assertNull($result);
    }

    private static function createClient(array $array = []): Client
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ClientId::fromString($string);

        $array = array_merge([
            'КВ-0004888', 'Лавров Олександр Генадійович', '24584810', 0, 9180004888, 'КВЦ0000161', '', 12435, 1, 'КРБК-010103', '2014-04-08',
        ], $array);

        $clientId = new Client1CId($array[0]);
        $fullName = new FullName($array[1]);
        $edrpouInn = new EdrpouInn($array[2]);
        $type = new Type($array[3]);
        $nktId = new NktId($array[4]);
        $managerId = new Manager1CId($array[5]);
        $agentId = new Agent1CId($array[6]);
        $fcCbrId = new FcCbrId($array[7]);
        $status = new Status($array[8]);
        $contractNumber = new ContractNumber($array[9]);
        $contractDate = new \DateTimeImmutable($array[10]);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return Client::createWithContract(
            $identity,
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
}
