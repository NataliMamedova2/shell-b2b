<?php

namespace App\Import\Application\FileDataSaver;

use DateTimeInterface;

interface ResultInterface
{

    /**
     * @return DateTimeInterface
     */
    public function getStartTime(): DateTimeInterface;

    /**
     * @return DateTimeInterface
     */
    public function getEndTime(): DateTimeInterface;

    /**
     * @return \DateInterval
     */
    public function getElapsed(): \DateInterval;

    /**
     * @return int
     */
    public function getMemoryUsage(): int;

    /**
     * @return int
     */
    public function getErrorCount(): int;

    /**
     * @return int
     */
    public function getSuccessCount(): int;

    /**
     * @return int
     */
    public function getTotalProcessedCount(): int;

    /**
     * @return \ArrayObject
     */
    public function getExceptions(): \ArrayObject;
}
