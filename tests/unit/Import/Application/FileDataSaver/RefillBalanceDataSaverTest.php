<?php

namespace Tests\Unit\Import\Application\FileDataSaver;

use App\Clients\Domain\RefillBalance\RefillBalance;
use App\Clients\Domain\RefillBalance\ValueObject\Amount;
use App\Clients\Domain\RefillBalance\ValueObject\CardOwner;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\RefillBalance\ValueObject\Operation;
use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use App\Import\Application\FileDataSaver\RefillBalanceDataSaver;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Tests\Unit\Clients\Domain\RefillBalance\RefillBalanceTest;

final class RefillBalanceDataSaverTest extends TestCase
{
    /**
     * @var EntityManagerInterface|ObjectProphecy
     */
    private $entityManagerMock;

    /**
     * @var RefillBalanceDataSaver
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

        $this->serviceObject = new RefillBalanceDataSaver($this->entityManagerMock->reveal(), $this->loggerMock->reveal(), $debug);
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(FileDataSaverInterface::class, $this->serviceObject);
    }

    public function testSupportFileMethodReturnTrue(): void
    {
        $this->assertEquals(true, $this->serviceObject->supportedFile('filename.tc'));
    }

    public function testSupportFileMethodReturnFalse(): void
    {
        $this->assertEquals(false, $this->serviceObject->supportedFile('filename.cc'));
    }

    public function testGetUniqueKeyFromEntityEmptyEntityReturnNull(): void
    {
        $result = $this->serviceObject->getUniqueKeyFromEntity(null);

        $this->assertEquals(null, $result);
    }

    public function testGetUniqueKeyFromEntityRefillBalanceEntityReturnId(): void
    {
        $entity = RefillBalanceTest::createValidEntity();

        $cardOwner = new CardOwner($entity->getCardOwner());
        $fcCbrId = new FcCbrId($entity->getFcCbrId());
        $operation = new Operation($entity->getOperation());
        $amount = new Amount($entity->getAmount());
        $date = $entity->getOperationDateTime();

        $data = [
            $cardOwner->getValue(),
            $fcCbrId->getValue(),
            $operation->getValue(),
            $amount->getValue(),
            $date->format('c'),
        ];

        $key = md5(implode('', $data));

        $result = $this->serviceObject->getUniqueKeyFromEntity($entity);

        $this->assertEquals($key, $result);
    }

    public function testBuildSelectQueryReturnQuery(): void
    {
        $array = [
            [
                2, '9180001322', '01', 1, '00000001500000', '24/06/19', '17:53:16',
            ]
        ];
        $items = new \ArrayIterator($array);

        $ids = [];
        foreach ($items as $record) {
            $ids[] = $this->serviceObject->getUniqueKeyFromRecord($record);
        }

        $abstractQueryMock = $this->prophesize(AbstractQuery::class);
        $this->entityManagerMock->createQuery(
            sprintf('SELECT c FROM %s c WHERE 
                         c.cardOwner IN (:cardOwner)
                         AND c.fcCbrId IN (:fcCbrId)
                         AND c.operation IN (:operation)
                         AND c.amount IN (:amount)
                         AND c.operationDate IN (:date)
                         ', RefillBalance::class))
            ->shouldBeCalled()
            ->willReturn($abstractQueryMock);

        $abstractQueryMock->setParameters([
            'cardOwner' => 2,
            'fcCbrId' => '9180001322',
            'operation' => 1,
            'amount' => 1500000,
            'date' => '2019-06-24 17:53:16',
        ]);

        $this->serviceObject->buildSelectQuery($this->entityManagerMock->reveal(), $items);
    }

    public function testCreateEntityReturnEntity(): void
    {
        $array = [
            2, '9180001322', '01', 1, '00000001500000', '24/06/19', '17:53:16',
        ];

        $result = $this->serviceObject->createEntity($array);

        $this->assertEquals(1500000, $result->getAmount());
        $this->assertEquals('+', $result->getOperationSign());
        $this->assertEquals('2019-06-24 17:53:16', $result->getOperationDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(2, $result->getCardOwner());
    }
}
