<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Application\Domain\ValueObject\Client1CId;
use PHPUnit\Framework\TestCase;

final class Client1CIdTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new Client1CId();
    }

    public function testCreateValidValueReturnObject(): void
    {
        $value = 'ТКЦ0000007';
        $result = new Client1CId($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreateEmptyValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = '';
        new Client1CId($value);
    }

    public function testCreateGreaterThan10LengthValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = 'ТКЦ0000007ТКЦ0000007';
        new Client1CId($value);
    }
}
