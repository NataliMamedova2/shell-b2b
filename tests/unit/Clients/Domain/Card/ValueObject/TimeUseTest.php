<?php

namespace Tests\Unit\Clients\Domain\Card\ValueObject;

use App\Clients\Domain\Card\ValueObject\TimeUse;
use PHPUnit\Framework\TestCase;

final class TimeUseTest extends TestCase
{
    public function testValidTime(): void
    {
        $formTime = new \DateTimeImmutable('00:00');
        $toTime = new \DateTimeImmutable('23:59');

        $result = new TimeUse($formTime, $toTime);

        $this->assertEquals($formTime->format('H:i'), $result->getStartTime()->format('H:i'));
        $this->assertEquals($toTime->format('H:i'), $result->getEndTime()->format('H:i'));
    }

    public function testStartTimeGreaterThanEndTimeReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $formTime = new \DateTimeImmutable('10:00');
        $toTime = new \DateTimeImmutable('09:59');

        new TimeUse($formTime, $toTime);
    }

    public function testStartTimeEqualToEndTimeReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $formTime = new \DateTimeImmutable('10:00');
        $toTime = new \DateTimeImmutable('09:59');

        new TimeUse($formTime, $toTime);
    }

    public function testNotValidEndTime(): void
    {
        $this->expectException(\Exception::class);

        $formTime = new \DateTimeImmutable('10:00');
        $toTime = new \DateTimeImmutable('25:00');

        new TimeUse($formTime, $toTime);
    }
}
