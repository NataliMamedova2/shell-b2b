<?php

namespace Tests\Unit\Clients\Domain\Driver\ValueObject;

use App\Clients\Domain\Driver\ValueObject\PhoneNumber;
use PHPUnit\Framework\TestCase;

final class PhoneNumberTest extends TestCase
{
    public function testCreateValidValueReturnObject(): void
    {
        $value = '+380934123312';
        $result = new PhoneNumber($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    /**
     * @param $value
     *
     * @dataProvider invalidDataProvider
     */
    public function testCreateInvalidValueReturnException($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new PhoneNumber($value);
    }

    public function invalidDataProvider()
    {
        return [
            'greater than 13' => ['+3934123321133'],
            'less length 13' => ['039341233211'],
            'invalid format' => ['3809341233123'],
        ];
    }
}
