<?php

namespace Tests\Unit\Clients\Domain\User\ValueObject;

use App\Clients\Domain\User\ValueObject\Username;
use PHPUnit\Framework\TestCase;

final class UsernameTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new Username();
    }

    /**
     * @param $value
     * @dataProvider validDataProvider
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new Username($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validDataProvider()
    {
        return [
            'minLength' => ['admin'],
            'maxLength' => [str_repeat('a', 30)],
            'regexp' => ['Login_uni2queName12'],
            'regexp2' => ['12345'],
        ];
    }

    /**
     * @param $value
     * @dataProvider invalidDataProvider
     */
    public function testCreateInvalidValueReturnObject($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Username($value);
    }

    public function invalidDataProvider()
    {
        return [
            'empty' => [''],
            'minLength' => ['adm'],
            'maxLength' => [str_repeat('a', 31)],
            'regexp' => ['Login-uni2queName12'],
        ];
    }
}
