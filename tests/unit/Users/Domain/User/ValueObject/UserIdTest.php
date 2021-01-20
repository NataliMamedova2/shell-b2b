<?php

namespace Tests\Unit\Users\Domain\User\ValueObject;

use App\Users\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function testFromValidString()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = UserId::fromString($string);

        static::assertInstanceOf(UserId::class, $identity);
        static::assertEquals($string, $identity->getId());
        static::assertEquals($string, (string) $identity);
    }

    public function testEquality()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identityOne = UserId::fromString($string);
        $identityTwo = UserId::fromString($string);
        $identityThree = UserId::next();
        static::assertTrue($identityOne->equalTo($identityTwo));
        static::assertFalse($identityTwo->equalTo($identityThree));
    }
}
