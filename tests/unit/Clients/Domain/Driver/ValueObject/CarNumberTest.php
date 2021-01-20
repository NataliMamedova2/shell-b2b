<?php

namespace Tests\Unit\Clients\Domain\Driver\ValueObject;

use App\Clients\Domain\Driver\ValueObject\CarNumber;
use PHPUnit\Framework\TestCase;

final class CarNumberTest extends TestCase
{
    /**
     * @param $value
     *
     * @dataProvider validDataProvider
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new CarNumber($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validDataProvider()
    {
        return [
            'ai1233211' => ['ai1233211'],
            'AI1233211' => ['AI1233288'],
            'Чй1233211' => ['Чй1233299'],
            'ЮЮ1233211' => ['ЮЮ1111111'],
        ];
    }

    /**
     * @param $value
     *
     * @dataProvider invalidDataProvider
     */
    public function testCreateInvalidValueReturnException($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CarNumber($value);
    }

    public function invalidDataProvider()
    {
        return [
            'max length' => ['ai12332111133'],
        ];
    }
}
