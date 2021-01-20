<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Clients\Domain\Client\ValueObject\Type;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    public function testCreateNotValidValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = 4;
        new Type($value);
    }

    public function provider()
    {
        return [
            'prepayment' => [0],
            'credit-line' => [1],
            'credit' => [2],
        ];
    }

    /**
     * @param $value
     * @dataProvider provider
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new Type($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }
}
