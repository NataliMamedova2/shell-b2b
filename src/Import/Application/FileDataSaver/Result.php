<?php

namespace App\Import\Application\FileDataSaver;

use DateTimeInterface;

final class Result implements ResultInterface
{
    /**
     * @var DateTimeInterface
     */
    private $startTime;

    /**
     * @var DateTimeInterface
     */
    private $endTime;

    /**
     * @var \DateInterval
     */
    private $elapsed;

    /**
     * @var int
     */
    private $errorCount = 0;

    /**
     * @var int
     */
    private $successCount = 0;

    /**
     * @var int
     */
    private $totalProcessedCount = 0;

    /**
     * @var int
     */
    private $memoryUsage;

    /**
     * @var \ArrayObject
     */
    private $exceptions;

    public function __construct(
        DateTimeInterface $startTime,
        DateTimeInterface $endTime,
        int $totalCount,
        int $memoryUsage,
        \ArrayObject $exceptions
    ) {
        $this->startTime = $startTime;
        $this->memoryUsage = $memoryUsage;
        $this->endTime = $endTime;
        $this->elapsed = $startTime->diff($endTime);
        $this->totalProcessedCount = $totalCount;
        $this->errorCount = count($exceptions);
        $this->successCount = $totalCount - $this->errorCount;
        $this->exceptions = $exceptions;
    }

    public function getStartTime(): DateTimeInterface
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTimeInterface
    {
        return $this->endTime;
    }

    public function getElapsed(): \DateInterval
    {
        return $this->elapsed;
    }

    public function getMemoryUsage(): int
    {
        return $this->memoryUsage;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getTotalProcessedCount(): int
    {
        return $this->totalProcessedCount;
    }

    public function getExceptions(): \ArrayObject
    {
        return $this->exceptions;
    }
}
