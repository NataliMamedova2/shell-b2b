<?php

namespace Tests\Unit\Clients\Domain\ClientInfo\ValueObject;

use App\Clients\Domain\ClientInfo\ValueObject\CreditLimit;
use PHPUnit\Framework\TestCase;

final class CreditLimitTest extends TestCase
{
    /**
     * @dataProvider providerValidValues
     *
     * @param $value
     */
    public function testCreateWithValidValuesReturnObject($value): void
    {
        $result = new CreditLimit($value);

        $this->assertEquals($value * 100, $result->getValue());
        $this->assertEquals($value * 100, $result->__toString());
    }

    public function providerValidValues()
    {
        return [
            'zero' => [0],
            'negative' => [-3287.78],
            'max length' => [99999999999999],
            'string' => ['-3287.78'],
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

        new CreditLimit($value);
    }

    public function providerInvalidValues()
    {
        return [
            'non-numeric' => ['string'],
            'greater than 14 chars' => [999999999999999],
        ];
    }
}
