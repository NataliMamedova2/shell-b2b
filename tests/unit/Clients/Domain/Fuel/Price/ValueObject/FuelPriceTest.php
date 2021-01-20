<?php

namespace Tests\Unit\Clients\Domain\Fuel\Price\ValueObject;

use App\Clients\Domain\Fuel\Price\ValueObject\FuelPrice;
use PHPUnit\Framework\TestCase;

final class FuelPriceTest extends TestCase
{

    public function testCreateReturnObject(): void
    {
        $value = 3200;
        $price = new FuelPrice($value);

        $this->assertEquals($value, $price->getValue());
    }

    /**
     * @param $value
     * @dataProvider providerInvalidValues
     */
    public function testCreateEmptyPriceReturnException($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new FuelPrice($value);
    }

    public function providerInvalidValues()
    {
        return [
            'zero' => [0],
            'negative' => [-2],
            'greater than 10 char' => [10000000000],
        ];
    }
}
