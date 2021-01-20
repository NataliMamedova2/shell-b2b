<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Clients\Domain\Client\ValueObject\Agent1CId;
use PHPUnit\Framework\TestCase;

final class Agent1CIdTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new Agent1CId();
    }

    public function testCreateValidValueReturnObject(): void
    {
        $value = 'АКЦ0000001';
        $result = new Agent1CId($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreateEmptyValueReturnObject(): void
    {
        $value = '';
        $result = new Agent1CId($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreateGreaterThan10LengthValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = 'АКЦ0000001АКЦ0000001';
        new Agent1CId($value);
    }
}
