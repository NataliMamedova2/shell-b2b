<?php

namespace Tests\Unit\Clients\Domain\Card\ValueObject;

use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use PHPUnit\Framework\TestCase;

final class ServiceScheduleTest extends TestCase
{
    public function testAllOneData(): void
    {
        $value = '1111111';

        $result = new ServiceSchedule($value);

        $this->assertEquals($value, $result->getValue());
    }

    public function testAllZeroData(): void
    {
        $value = '0000000';

        $result = new ServiceSchedule($value);

        $this->assertEquals($value, $result->getValue());
    }

    public function testLongLengthReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $value = '11111111';

        new ServiceSchedule($value);
    }

    public function testNotAllowedCharacterReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $value = '2111111';

        new ServiceSchedule($value);
    }
}
