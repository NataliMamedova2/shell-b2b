<?php

namespace Tests\Unit\Clients\Infrastructure\ClientInfo\Service\Balance;

use App\Clients\Infrastructure\ClientInfo\Service\Balance\Balance;
use PHPUnit\Framework\TestCase;

final class BalanceTest extends TestCase
{
    public function testBalanceZeroValue(): void
    {
        $value = 0;
        $result = new Balance($value);

        $this->assertEquals(0, $result->getValue());
        $this->assertEquals(0, $result->getAbsoluteValue());
        $this->assertEquals('+', $result->getSign());
    }

    public function testBalancePositiveValue(): void
    {
        $value = 423234;
        $result = new Balance($value);

        $this->assertEquals(423234, $result->getValue());
        $this->assertEquals(423234, $result->getAbsoluteValue());
        $this->assertEquals('+', $result->getSign());
    }

    public function testBalanceNegativeValue(): void
    {
        $value = -423234;
        $result = new Balance($value);

        $this->assertEquals(-423234, $result->getValue());
        $this->assertEquals(423234, $result->getAbsoluteValue());
        $this->assertEquals('-', $result->getSign());
    }
}
