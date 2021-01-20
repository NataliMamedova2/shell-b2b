<?php

namespace Tests\Unit\Clients\Domain\ClientInfo\ValueObject;

use App\Clients\Domain\ClientInfo\ValueObject\ClientPcId;
use PHPUnit\Framework\TestCase;

final class ClientPcIdTest extends TestCase
{
    /**
     * @dataProvider providerValidValues
     *
     * @param $value
     */
    public function testCreateWithValidValuesReturnObject($value): void
    {
        $result = new ClientPcId($value);

        $this->assertEquals((int) $value, $result->getValue());
        $this->assertEquals((string) \intval($value), $result->__toString());
    }

    public function providerValidValues()
    {
        return [
            'min' => [1],
            'string' => ['009170004272'],
            'number' => [9170004272],
            'max length' => [999999999999],
        ];
    }

    /**
     * @dataProvider providerInvalidValues
     *
     * @param $value
     */
    public function testCreateWithInvalidValuesReturnException($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ClientPcId($value);
    }

    public function providerInvalidValues()
    {
        return [
            'zero' => [0],
            'empty' => [''],
            'greater than 12 chars' => [9999999999999],
            'non-numeric' => ['string'],
        ];
    }
}
