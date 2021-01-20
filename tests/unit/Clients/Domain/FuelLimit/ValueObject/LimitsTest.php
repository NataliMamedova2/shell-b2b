<?php

namespace Tests\Unit\Clients\Domain\FuelLimit\ValueObject;

use App\Clients\Domain\FuelLimit\ValueObject\Limits;
use PHPUnit\Framework\TestCase;

final class LimitsTest extends TestCase
{
    public function testDayLimitEqualMinLimitValue(): void
    {
        $dayLimit = 100;
        $weekLimit = 100;
        $monthLimit = 100;

        $result = new Limits($dayLimit, $weekLimit, $monthLimit);

        $this->assertEquals($dayLimit * 100, $result->getDayLimit());
        $this->assertEquals($weekLimit * 100, $result->getWeekLimit());
        $this->assertEquals($monthLimit * 100, $result->getMonthLimit());

        $this->assertIsInt($result->getDayLimit());
    }

    public function testEqualsReturnTrue(): void
    {
        $currentMoneyLimit = new Limits(100, 100, 100);

        $otherMoneyLimit = new Limits('100', '100', '100');

        $this->assertTrue($currentMoneyLimit->equals($otherMoneyLimit));
        $this->assertTrue($otherMoneyLimit->equals($currentMoneyLimit));
    }

    public function testEqualsReturnFalse(): void
    {
        $currentMoneyLimit = new Limits(100, 100, 100);

        $otherMoneyLimit = new Limits(99, '100', '100');

        $this->assertFalse($currentMoneyLimit->equals($otherMoneyLimit));
        $this->assertFalse($otherMoneyLimit->equals($currentMoneyLimit));
    }

    /**
     * @dataProvider providerValidValues
     *
     * @param $dayLimit
     * @param $weekLimit
     * @param $monthLimit
     */
    public function testCreateWithValidValuesReturnException($dayLimit, $weekLimit, $monthLimit): void
    {
        $result = new Limits($dayLimit, $weekLimit, $monthLimit);

        $this->assertEquals($dayLimit * 100, $result->getDayLimit());
        $this->assertEquals($weekLimit * 100, $result->getWeekLimit());
        $this->assertEquals($monthLimit * 100, $result->getMonthLimit());
    }

    public function providerValidValues()
    {
        return [
            'min value' => [1, 1, 1],
            'string' => ['10', '20', '2'],
            'max length' => [99999999999999, 99999999999999, 99999999999999],
        ];
    }

    /**
     * @dataProvider providerInvalidValues
     *
     * @param $dayLimit
     * @param $weekLimit
     * @param $monthLimit
     */
    public function testCreateWithInvalidValuesReturnException($dayLimit, $weekLimit, $monthLimit): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Limits($dayLimit, $weekLimit, $monthLimit);
    }

    public function providerInvalidValues()
    {
        return [
            'zero' => [0, 0, 0],
            'negative' => [-2, -1, -12],
            'greater than 14 chars' => [999999999999999, 999999999999999, 999999999999999],
        ];
    }
}
