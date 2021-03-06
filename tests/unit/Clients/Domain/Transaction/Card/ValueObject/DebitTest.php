<?php

namespace Tests\Unit\Clients\Domain\Transaction\Card\ValueObject;

use App\Clients\Domain\Transaction\Card\ValueObject\Debit;
use PHPUnit\Framework\TestCase;

final class DebitTest extends TestCase
{
    /**
     * @param $value
     * @dataProvider validProviderValues
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new Debit($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validProviderValues()
    {
        return [
            'min value' => [1],
            'string ' => ['2'],
            'max length' => [999999999999999999],
        ];
    }

    /**
     * @param $value
     * @dataProvider invalidProviderValues
     */
    public function testCreateEmptyPriceReturnException($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Debit($value);
    }

    public function invalidProviderValues()
    {
        return [
            'zero' => [0],
            'negative' => [-2],
            'out of max length' => [9223372036854775807],
        ];
    }
}
