<?php

namespace App\Import\Application\FileDataSaver\Writer;

use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use App\Import\Application\FileDataSaver\Result;
use App\Import\Application\FileDataSaver\ResultInterface;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\ORM\EntityManagerInterface;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Psr\Log\LoggerInterface;

abstract class DoctrineEntityWriter implements FileDataSaverInterface
{

    private const RECORDS_CHUNK_SIZE = 50000;

    /**
     * @var int
     */
    protected $batchSize = 500;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \ArrayObject
     */
    private $exceptions;

    /**
     * @var SQLLogger|null
     */
    private $doctrineLogger;

    /**
     * @var bool
     */
    private $debug;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, bool $debug = false)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->debug = $debug;
    }

    public function recordsChunkSize(): int
    {
        return self::RECORDS_CHUNK_SIZE;
    }

    public function handle(\Iterator $records): ResultInterface
    {
        gc_enable();

        $this->disableLogging();

        $startMemoryPeakUsage = memory_get_peak_usage(true);
        $startTime = new \DateTimeImmutable();

        $arrayIterator = new \ArrayIterator();
        foreach ($records as $index => $record) {
            $index = $this->getUniqueKeyFromRecord($record);
            if (null !== $index) {
                $arrayIterator->offsetSet($index, $record);

                continue;
            }

            $arrayIterator->append($record);
        }

        $totalCount = $arrayIterator->count();
        unset($records);

        $this->handleArray($arrayIterator);

        $endTime = new \DateTimeImmutable();
        $endMemoryPeakUsage = memory_get_peak_usage(true);
        $memoryUsage = $endMemoryPeakUsage - $startMemoryPeakUsage;

        $this->enableLogging();

        gc_collect_cycles();

        return new Result($startTime, $endTime, $totalCount, $memoryUsage, $this->getExceptions());
    }

    /**
     * Return unique key from record for matching data in record and db.
     *
     * @param array $record
     *
     * @return string
     */
    public function getUniqueKeyFromRecord(array $record): ?string
    {
        return null;
    }

    abstract public function handleArray(\ArrayIterator $arrayIterator);

    private function disableLogging(): void
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $this->doctrineLogger = $config->getSQLLogger();
        $config->setSQLLogger(null);
    }

    protected function addException(\Throwable $throwable): void
    {
        if (true === $this->debug) {
            throw $throwable;
        }

        $value = [
            'message' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
        ];
        $this->getExceptions()->append($value);

        $this->logger->critical($throwable->getMessage());
    }

    private function getExceptions(): \ArrayObject
    {
        if (!$this->exceptions instanceof \ArrayObject) {
            $this->exceptions = new \ArrayObject();
        }

        return $this->exceptions;
    }

    protected function insert(\ArrayIterator $arrayIterator): void
    {
        $results = function () use ($arrayIterator) {
            foreach ($arrayIterator as $record) {
                try {
                    $entity = $this->createEntity($record);

                    if (!empty($entity)) {
                        $this->entityManager->persist($entity);

                        yield $entity;
                    }
                } catch (\Exception $e) {
                    $this->addException($e);
                }
            }
        };

        $insertIterator = SimpleBatchIteratorAggregate::fromTraversableResult(
            $results(),
            $this->entityManager,
            $this->batchSize
        );

        try {
            foreach ($insertIterator as $entity) {
            }
        } catch (\Exception $e) {
            $this->addException($e);
        }
    }

    /**
     * Return new Entity object with data from record.
     *
     * @param array $record
     *
     * @return object
     */
    abstract public function createEntity(array $record): ?object;

    private function enableLogging(): void
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $config->setSQLLogger($this->doctrineLogger);
    }
}
