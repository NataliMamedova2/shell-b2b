<?php

namespace Tests\Unit\Clients\Domain\FuelLimit\ValueObject;

use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use PHPUnit\Framework\TestCase;

final class PurseActivityTest extends TestCase
{
    /**
     * @param $value
     *
     * @dataProvider validDataProvider
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new PurseActivity($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validDataProvider(): array
    {
        return [
            'active' => [0],
            'inactive' => [1],
            'mark for removal' => [2],
        ];
    }

    public function testActiveReturnObject(): void
    {
        $activeValue = 0;
        $result = PurseActivity::active();

        $this->assertEquals($activeValue, $result->getValue());
        $this->assertEquals($activeValue, $result->__toString());
        $this->assertEquals($activeValue, (string) $result);
    }

    public function testMarkToRemoveReturnObject(): void
    {
        $markToRemoveValue = 2;
        $result = PurseActivity::markToRemove();

        $this->assertEquals($markToRemoveValue, $result->getValue());
        $this->assertEquals($markToRemoveValue, $result->__toString());
        $this->assertEquals($markToRemoveValue, (string) $result);
    }

    public function testCreateInvalidValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $invalidValue = 3;
        new PurseActivity($invalidValue);
    }
}
