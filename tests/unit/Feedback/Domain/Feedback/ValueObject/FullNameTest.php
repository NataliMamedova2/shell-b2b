<?php

namespace Tests\Unit\Feedback\Domain\Feedback\ValueObject;

use App\Feedback\Domain\Feedback\ValueObject\FullName;
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
        $value = 'Семен Семеныч';
        $result = new FullName($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreate50LengthValueReturnException(): void
    {
        $value = str_repeat('w', 50);
        $result = new FullName($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function testCreate165LengthValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = str_repeat('w', 165);
        new FullName($value);
    }

    public function testCreateEmptyValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = '';
        new FullName($value);
    }
}
