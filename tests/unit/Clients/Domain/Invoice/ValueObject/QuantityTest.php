<?php

namespace Tests\Unit\Clients\Domain\Invoice\ValueObject;

use App\Clients\Domain\Invoice\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class QuantityTest extends TestCase
{
    public function testCreateReturnObject(): void
    {
        $value = '20';
        $price = new Quantity($value);

        $this->assertEquals($value * 100, $price->getValue());
    }

    public function testCreateMinValueReturnObject(): void
    {
        $value = 0.1;
        $price = new Quantity($value);

        $this->assertEquals($value * 100, $price->getValue());
    }

    public function testCreateMaxValueReturnObject(): void
    {
        $value = 50000;
        $price = new Quantity($value);

        $this->assertEquals($value * 100, $price->getValue());
    }

    /**
     * @param $value
     * @dataProvider providerInvalidValues
     */
    public function testCreateEmptyPriceReturnException($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Quantity($value);
    }

    public function providerInvalidValues()
    {
        return [
            'value is zero' => [0],
            'value is negative' => [-2],
            'max value' => [50001],
        ];
    }
}
