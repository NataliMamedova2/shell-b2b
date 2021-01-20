<?php

namespace Tests\Unit\Api\Domain\Log\ValueObject;

use App\Api\Domain\Log\ValueObject\LogId;
use PHPUnit\Framework\TestCase;

class LogIdTest extends TestCase
{
    public function testFromValidString()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = LogId::fromString($string);

        static::assertInstanceOf(LogId::class, $identity);
        static::assertEquals($string, $identity->getId());
        static::assertEquals($string, (string) $identity);
    }

    public function testEquality()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identityOne = LogId::fromString($string);
        $identityTwo = LogId::fromString($string);
        $identityThree = LogId::next();
        static::assertTrue($identityOne->equalTo($identityTwo));
        static::assertFalse($identityTwo->equalTo($identityThree));
    }
}
