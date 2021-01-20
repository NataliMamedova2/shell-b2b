<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Clients\Domain\Client\ValueObject\Manager1CId;
use PHPUnit\Framework\TestCase;

final class Manager1CIdTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new Manager1CId();
    }

    public function testCreateValidValueReturnObject(): void
    {
        $value = 'МКЦ0000001';
        $result = new Manager1CId($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreateGreaterThan10LengthValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = 'МКЦ0000001МКЦ0000001';
        new Manager1CId($value);
    }
}
