<?php

namespace Tests\Unit\Clients\Domain\ClientInfo\ValueObject;

use App\Clients\Domain\ClientInfo\ValueObject\Balance;
use PHPUnit\Framework\TestCase;

final class BalanceTest extends TestCase
{
    /**
     * @dataProvider providerValidValues
     *
     * @param $value
     */
    public function testCreateWithValidValuesReturnObject($value): void
    {
        $result = new Balance($value);

        $this->assertEquals($value * 100, $result->getValue());
        $this->assertEquals($value * 100, $result->__toString());
    }

    public function providerValidValues()
    {
        return [
            'zero' => [0],
            'negative' => [-328778],
            'max length' => [99999999999999],
            'string' => ['-328778'],
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

        new Balance($value);
    }

    public function providerInvalidValues()
    {
        return [
            'non-numeric' => ['string'],
            'greater than 14 chars' => [999999999999999],
        ];
    }
}
