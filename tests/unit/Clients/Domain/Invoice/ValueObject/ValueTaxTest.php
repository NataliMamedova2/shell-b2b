<?php

namespace Tests\Unit\Clients\Domain\Invoice\ValueObject;

use App\Clients\Domain\Invoice\ValueObject\ValueTax;
use PHPUnit\Framework\TestCase;

final class ValueTaxTest extends TestCase
{
    public function testCreateReturnObject(): void
    {
        $value = '20';
        $price = new ValueTax($value);

        $this->assertEquals($value * 100, $price->getValue());
    }

    /**
     * @param $value
     * @dataProvider providerInvalidValues
     */
    public function testCreateEmptyPriceReturnException($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ValueTax($value);
    }

    public function providerInvalidValues()
    {
        return [
            'value is zero' => [0],
            'value is negative' => [-2],
        ];
    }
}
