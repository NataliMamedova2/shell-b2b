<?php

namespace Tests\Unit\Clients\Domain\User\ValueObject;

use App\Clients\Domain\User\ValueObject\Status;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new Status();
    }

    public function testCreateNotValidValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = 2;
        new Status($value);
    }

    public function testCreateActiveReturnObject(): void
    {
        $activeValue = 1;
        $result = Status::active();

        $this->assertEquals($activeValue, $result->getValue());
        $this->assertEquals($activeValue, $result->__toString());
        $this->assertEquals($activeValue, (string) $result);
    }

    /**
     * @param $value
     * @dataProvider validDataProvider
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new Status($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validDataProvider(): array
    {
        return [
            'active' => [1],
            'blocked' => [0],
        ];
    }

    /**
     * @param string $name
     * @param int    $value
     * @dataProvider fromNameDataProvider
     */
    public function testCreateFromNameReturnObject(string $name, int $value): void
    {
        $result = Status::fromName($name);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function fromNameDataProvider(): array
    {
        return [
            ['active', 1],
            ['blocked', 0],
        ];
    }
}
