<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Clients\Domain\Client\ValueObject\FullName;
use PHPUnit\Framework\TestCase;

final class FullNameTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new FullName();
    }

    public function testCreateValidValueReturnObject(): void
    {
        $value = 'ТОВАРИСТВО З ОБМЕЖЕНОЮ ВІДПОВІДАЛЬНІСТЮ "ЕНЕРПРОФІТ"';
        $result = new FullName($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreate501LengthValueReturnObject(): void
    {
        $value = str_repeat('w', 500);
        $result = new FullName($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreate501LengthValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = str_repeat('w', 501);
        new FullName($value);
    }

    public function testCreateEmptyValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = '';
        new FullName($value);
    }
}
