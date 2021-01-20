<?php

namespace App\Import\Domain\Import\File;

use App\Import\Application\FileDataSaver\ResultInterface;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class Result
{
    /**
     * @var \DateTimeInterface|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $startTime;

    /**
     * @var DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $endTime;

    /**
     * @var \DateInterval
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $elapsed;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $memoryUsage = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $errorCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $successCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalProcessedCount = 0;

    /**
     * @var array|null
     *
     * @ORM\Column(type="json", options={"jsonb": true}, nullable=true)
     */
    private $exceptions;

    private function __construct(DateTimeInterface $startTime)
    {
        $this->startTime = $startTime;
        $this->memoryUsage = 0;
        $this->totalProcessedCount = 0;
        $this->errorCount = 0;
        $this->successCount = 0;
        $this->exceptions = [];
    }

    public static function create(DateTimeInterface $startTime): self
    {
        return new self($startTime);
    }

    public function merge(ResultInterface $result): void
    {
        $this->endTime = $result->getEndTime();
        $this->elapsed = $this->startTime->diff($this->endTime);

        $this->memoryUsage += $result->getMemoryUsage();
        $this->totalProcessedCount += $result->getTotalProcessedCount();
        $this->errorCount += $result->getErrorCount();
        $this->successCount += $result->getSuccessCount();
        $this->exceptions += $result->getExceptions()->getArrayCopy();
    }

    public function getExceptions(): ?array
    {
        return $this->exceptions;
    }

    public function getElapsed(): ?\DateInterval
    {
        return $this->elapsed;
    }

    public function getStartTime(): ?DateTimeInterface
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTimeInterface
    {
        return $this->endTime;
    }

    public function getSuccessCount(): int
    {
        return (int) $this->successCount;
    }

    public function getTotalProcessedCount(): int
    {
        return (int) $this->totalProcessedCount;
    }

    public function getMemoryUsage(): int
    {
        return (int) $this->memoryUsage;
    }

    public function getErrorCount(): int
    {
        return (int) $this->errorCount;
    }
}
