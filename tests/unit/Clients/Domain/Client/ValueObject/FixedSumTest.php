<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Clients\Domain\Client\ValueObject\FixedSum;
use PHPUnit\Framework\TestCase;

final class FixedSumTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new FixedSum();
    }

    public function testCreateZeroValueReturnException(): void
    {
        $value = 0;
        $result = new FixedSum($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreateValidValueReturnObject(): void
    {
        $value = 9180004888;
        $result = new FixedSum($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreate14LengthValueReturnException(): void
    {
        $value = 12345678901234;
        $result = new FixedSum($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreate15LengthValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = 123456789012345;
        new FixedSum($value);
    }

    public function testCreateNegativeValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = -1;
        new FixedSum($value);
    }
}
