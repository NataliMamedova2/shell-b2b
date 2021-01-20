<?php

namespace Tests\Unit\Application\Domain\ValueObject;

use App\Application\Domain\ValueObject\FcCbrId;
use PHPUnit\Framework\TestCase;

final class FcCbrIdTest extends TestCase
{
    public function testCreateValidValueReturnObject(): void
    {
        $value = '0000000001';
        $result = new FcCbrId($value);

        $this->assertEquals(1, $result->getValue());
        $this->assertEquals('1', $result->__toString());
        $this->assertEquals(1, (string) $result);
    }

    /**
     * @dataProvider providerValidValues
     *
     * @param $value
     */
    public function testCreateWithValidValuesReturnObject($value): void
    {
        $result = new FcCbrId($value);

        $this->assertEquals((int) $value, $result->getValue());
        $this->assertEquals((string) \intval($value), $result->__toString());
    }

    public function providerValidValues()
    {
        return [
            'empty' => [''],
            'zero' => [0],
            'min' => [1],
            'string' => ['000000004171'],
            'number' => [4171],
            'max length' => [999999999999],
            'non-numeric' => ['string'],
        ];
    }

    /**
     * @dataProvider providerInvalidValues
     *
     * @param $value
     */
    public function testCreateWithInvalidValuesReturnException($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new FcCbrId($value);
    }

    public function providerInvalidValues()
    {
        return [
            'negative' => [-20],
            'greater than 12 chars' => [9999999999999],
        ];
    }
}
