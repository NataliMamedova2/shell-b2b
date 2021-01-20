<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Clients\Domain\Client\ValueObject\EckDsgCa;
use PHPUnit\Framework\TestCase;

final class EckDsgCaTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new EckDsgCa();
    }

    public function testCreateValidValueReturnObject(): void
    {
        $value = 1;
        $result = new EckDsgCa($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreateZeroValueReturnException(): void
    {
        $value = 0;
        $result = new EckDsgCa($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreate2LengthValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = 12;
        new EckDsgCa($value);
    }

    public function testCreateNegativeValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = -1;
        new EckDsgCa($value);
    }
}
