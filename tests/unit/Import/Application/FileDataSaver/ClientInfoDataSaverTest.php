<?php

namespace Tests\Unit\Import\Application\FileDataSaver;

use App\Clients\Domain\ClientInfo\ClientInfo;
use PHPUnit\Framework\TestCase;
use App\Import\Application\FileDataSaver\ClientInfoDataSaver;
use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

final class ClientInfoDataSaverTest extends TestCase
{
    /**
     * @var EntityManagerInterface|ObjectProphecy
     */
    private $entityManagerMock;

    /**
     * @var ClientInfoDataSaver
     */
    private $serviceObject;

    /**
     * @var ObjectProphecy|LoggerInterface
     */
    private $loggerMock;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->loggerMock = $this->prophesize(LoggerInterface::class);
        $debug = false;

        $this->serviceObject = new ClientInfoDataSaver($this->entityManagerMock->reveal(), $this->loggerMock->reveal(), $debug);
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(FileDataSaverInterface::class, $this->serviceObject);
    }

    public function testSupportFileMethodReturnTrue(): void
    {
        $this->assertEquals(true, $this->serviceObject->supportedFile('PIDCLi_R.txt'));
    }

    public function testSupportFileMethodReturnFalse(): void
    {
        $this->assertEquals(false, $this->serviceObject->supportedFile('filename.cc'));
    }

    public function testFilterRecordsReturnTrue(): void
    {
        $array = ['&'];
        $this->assertEquals(true, $this->serviceObject->filterRecords($array));
    }

    public function testFilterRecordsReturnFalse(): void
    {
        $array = ['@'];
        $this->assertEquals(false, $this->serviceObject->filterRecords($array));
    }

    public function testGetUniqueKeyFromEntityEmptyEntityReturnNull(): void
    {
        $result = $this->serviceObject->getUniqueKeyFromEntity(null);

        $this->assertEquals(null, $result);
    }

    public function testGetUniqueKeyFromEntityReturnClientPcId(): void
    {
        $entityMock = $this->prophesize(ClientInfo::class);

        $key = 9170004272;
        $entityMock->getClientPcId()
            ->shouldBeCalled()
            ->willReturn($key);

        $result = $this->serviceObject->getUniqueKeyFromEntity($entityMock->reveal());

        $this->assertEquals($key, $result);
    }

    public function testBuildSelectQueryReturnQuery(): void
    {
        $array = [
            ['&', 2, '009170000001', '000000001001', 29.43, '06/07/2012', '19:47:16', 0, 1],
            ['@', 2, '009170000001', '000000001001', 1, 0, 100000, 700000, 3000000, 0, 0, 0],
            ['@', 2, '009170000001', '000000001001', 0, 1, 100000, 700000, 3000000, 0, 0, 0],
            ['&', 2, '009170000002', '000000001002', 10, '06/07/2012', '19:47:16', 100, 1],
        ];
        $items = new \ArrayIterator($array);

        $abstractQueryMock = $this->prophesize(AbstractQuery::class);
        $this->entityManagerMock->createQuery(
            sprintf('SELECT c FROM %s c WHERE c.clientPcId IN (:ids)', ClientInfo::class)
        )
            ->shouldBeCalled()
            ->willReturn($abstractQueryMock);

        $abstractQueryMock->setParameters([
            'ids' => ['9170000001', '009170000002'],
        ]);

        $this->serviceObject->buildSelectQuery($this->entityManagerMock->reveal(), $items);
    }

    public function testCreateEntityDataNotValidReturnNull(): void
    {
        $array = [
            2, '9180001322', '01', 1, '00000001500000', '24/06/19', '17:53:16',
        ];

        $result = $this->serviceObject->createEntity($array);

        $this->assertNull($result);
    }

    public function testCreateEntityDataValidReturnEntity(): void
    {
        $record = ['&', 2, '009170000001', '000000001001', 29.43, '06/07/2012', '19:47:16', 100, 1];

        $result = $this->serviceObject->createEntity($record);

        $this->assertEquals(9170000001, $result->getClientPcId());
        $this->assertEquals(2943, $result->getBalance());
        $this->assertEquals(10000, $result->getCreditLimit());
    }
}
