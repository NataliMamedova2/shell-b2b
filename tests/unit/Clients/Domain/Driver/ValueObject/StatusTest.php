<?php

namespace Tests\Unit\Clients\Domain\Driver\ValueObject;

use App\Clients\Domain\Driver\ValueObject\Status;
use PHPUnit\Framework\TestCase;

final class StatusTest extends TestCase
{
    /**
     * @param $value
     * @param $name
     *
     * @dataProvider validDataProvider
     */
    public function testCreateValidValueReturnObject($value, $name): void
    {
        $result = new Status($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validDataProvider()
    {
        return [
            'blocked' => [0, 'blocked'],
            'active' => [1, 'active'],
        ];
    }

    /**
     * @param $name
     * @param $value
     *
     * @dataProvider fromNameValidDataProvider
     */
    public function testCreateFromNameValidValueReturnObject($name, $value): void
    {
        $result = Status::fromName($name);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function fromNameValidDataProvider()
    {
        return [
            'blocked' => ['blocked', 0],
            'active' => ['active', 1],
        ];
    }

    public function testActiveReturnObject(): void
    {
        $activeValue = 1;
        $result = Status::active();

        $this->assertEquals($activeValue, $result->getValue());

        $activeName = 'active';
        $this->assertEquals($activeName, $result->getName());
        $this->assertEquals($activeValue, $result->__toString());
        $this->assertEquals($activeValue, (string) $result);
    }

    /**
     * @param $value
     * @dataProvider invalidDataProvider
     */
    public function testCreateInvalidValueReturnException($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Status($value);
    }

    public function invalidDataProvider()
    {
        return [
            'negative' => [-1],
            'more than max' => [3],
        ];
    }
}
