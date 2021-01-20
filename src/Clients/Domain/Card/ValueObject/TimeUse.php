<?php

namespace App\Clients\Domain\Card\ValueObject;

final class TimeUse
{
    /**
     * @var \DateTimeInterface
     */
    private $startTime;

    /**
     * @var \DateTimeInterface
     */
    private $endTime;

    public function __construct(\DateTimeInterface $startTime, \DateTimeInterface $endTime)
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;

        if ($this->getStartTime()->getTimestamp() >= $this->getEndTime()->getTimestamp()) {
            throw new \InvalidArgumentException('EntTime must be greater than StartTime');
        }
    }

    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }

    public function getEndTime(): \DateTimeInterface
    {
        return $this->endTime;
    }

    public function equals(self $other): bool
    {
        return $this->getStartTime()->getTimestamp() === $other->getStartTime()->getTimestamp() &&
            $this->getEndTime()->getTimestamp() === $other->getEndTime()->getTimestamp();
    }
}
