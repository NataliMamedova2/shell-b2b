<?php

namespace Tests\Unit\Import\Application\FileDataSaver;

use App\Import\Application\FileDataSaver\Writer\DoctrineEntityWriter;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class AbstractDoctrineEntityWriterTest extends TestCase
{
    protected $newAnonymousClassFromAbstract;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|EntityManagerInterface
     */
    private $entityManagerMock;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|LoggerInterface
     */
    private $loggerMock;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->loggerMock = $this->prophesize(LoggerInterface::class);
        $debug = false;

        $this->newAnonymousClassFromAbstract = new class($this->entityManagerMock->reveal(), $this->loggerMock->reveal(), $debug) extends DoctrineEntityWriter {
            public function getUniqueKeyFromRecord(array $record): string
            {
                return $record[0];
            }

            public function handleArray(\ArrayIterator $arrayIterator)
            {
            }

            public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery
            {
            }

            public function getUniqueKeyFromEntity(object $entity): ?string
            {
            }

            public function updateEntity(object $entity, array $record): void
            {
            }

            public function createEntity(array $record): object
            {
            }

            public function supportedFile(string $extension): bool
            {
                return true;
            }
        };
    }

    public function testAbstractClassGetEntityClassMethod(): void
    {
        $record = [
            'uniqueKey',
            'second',
        ];

        $this->assertEquals($record[0], $this->newAnonymousClassFromAbstract->getUniqueKeyFromRecord($record));
    }

    public function testDisableLoggingMethod(): void
    {
        $connectionMock = $this->prophesize(Connection::class);
        $configurationMock = $this->prophesize(Configuration::class);
        $sqlLoggerMock = $this->prophesize(SQLLogger::class);

        $this->entityManagerMock->getConnection()
            ->shouldBeCalled()
            ->willReturn($connectionMock);

        $connectionMock->getConfiguration()
            ->shouldBeCalled()
            ->willReturn($configurationMock);

        $configurationMock->getSQLLogger()
            ->shouldBeCalled()
            ->willReturn($sqlLoggerMock);

        $configurationMock->setSQLLogger(null)
            ->shouldBeCalled();

        $this->invokeMethod($this->newAnonymousClassFromAbstract, 'disableLogging');
    }

    public function testEnableLoggingMethod(): void
    {
        $connectionMock = $this->prophesize(Connection::class);
        $configurationMock = $this->prophesize(Configuration::class);
        $sqlLoggerMock = $this->prophesize(SQLLogger::class);

        $this->entityManagerMock->getConnection()
            ->shouldBeCalled()
            ->willReturn($connectionMock);

        $connectionMock->getConfiguration()
            ->shouldBeCalled()
            ->willReturn($configurationMock);

        $reflection = new \ReflectionClass(DoctrineEntityWriter::class);
        $property = $reflection->getProperty('doctrineLogger');
        $property->setAccessible(true);
        $property->setValue($this->newAnonymousClassFromAbstract, $sqlLoggerMock->reveal());

        $configurationMock->setSQLLogger($sqlLoggerMock->reveal())
            ->shouldBeCalled();

        $this->invokeMethod($this->newAnonymousClassFromAbstract, 'enableLogging');
    }

    public function testAddExceptionsMethod(): void
    {
        $throwable = new \Exception('message text');
        $value = [
            'message' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
        ];

        $exception = $this->invokeMethod($this->newAnonymousClassFromAbstract, 'getExceptions');
        $exception->append($value);

        $this->invokeMethod($this->newAnonymousClassFromAbstract, 'addException', [$throwable]);

        $this->assertEquals($value['message'], $exception->offsetGet(0)['message']);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on
     * @param string $methodName Method name to call
     * @param array  $parameters array of parameters to pass into method
     *
     * @return mixed method return
     *
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
