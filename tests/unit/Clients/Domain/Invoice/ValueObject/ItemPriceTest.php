<?php

namespace Tests\Unit\Clients\Domain\Invoice\ValueObject;

use App\Clients\Domain\Invoice\ValueObject\ItemPrice;
use PHPUnit\Framework\TestCase;

final class ItemPriceTest extends TestCase
{

    public function testCreateReturnObject(): void
    {
        $value = '32';
        $price = new ItemPrice($value);

        $this->assertEquals($value * 100, $price->getValue());
    }

    /**
     * @param $value
     * @dataProvider providerInvalidValues
     */
    public function testCreateEmptyPriceReturnException($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ItemPrice($value);
    }

    public function providerInvalidValues()
    {
        return [
            'value is zero' => [0],
            'value is negative' => [-2],
            'greater than 10 char' => [10000000000],
        ];
    }
}
