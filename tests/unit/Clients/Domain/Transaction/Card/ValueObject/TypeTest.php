<?php

namespace Tests\Unit\Clients\Domain\Transaction\Card\ValueObject;

use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    /**
     * @param $value
     * @dataProvider validProviderValues
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new Type($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validProviderValues()
    {
        return [
            'write-off' => [0],
            'return' => [1],
            'replenishment' => [2],
            'replenishment as string value' => ['2'],
        ];
    }

    /**
     * @param $value
     * @dataProvider invalidProviderValues
     */
    public function testCreateEmptyPriceReturnException($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Type($value);
    }

    public function invalidProviderValues()
    {
        return [
            'negative' => [-2],
            'not allowed' => [23],
        ];
    }

    public function testCreateFromNameValidNameReturnObject(): void
    {
        $typeReturnValue = 1;
        $typeReturnName = 'return';
        $result = Type::fromName($typeReturnName);

        $this->assertEquals($typeReturnName, $result->getName());
        $this->assertEquals($typeReturnValue, $result->getValue());
        $this->assertEquals($typeReturnValue, $result->__toString());
        $this->assertEquals($typeReturnValue, (string) $result);
    }
}
