<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Clients\Domain\Client\ValueObject\NktId;
use PHPUnit\Framework\TestCase;

final class NktIdTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new NktId();
    }

    public function testCreateValidValueReturnObject(): void
    {
        $value = 9180004888;
        $result = new NktId($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreate12LengthValueReturnException(): void
    {
        $value = 123456789012;
        $result = new NktId($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreate13LengthValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = 1234567890123;
        new NktId($value);
    }

    public function testCreateNegativeValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = -1;
        new NktId($value);
    }
}
