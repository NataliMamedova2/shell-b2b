<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Clients\Domain\Client\ValueObject\EdrpouInn;
use PHPUnit\Framework\TestCase;

class EdrpouInnTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new EdrpouInn();
    }

    public function testCreateValidValueReturnObject(): void
    {
        $value = '24584810';
        $result = new EdrpouInn($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreate501LengthValueReturnObject(): void
    {
        $value = '245848104589';
        $result = new EdrpouInn($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreate501LengthValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value ='24584810458967';
        new EdrpouInn($value);
    }
}