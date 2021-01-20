<?php

namespace Tests\Unit\Clients\Domain\Driver\ValueObject;

use App\Clients\Domain\Driver\ValueObject\DriverId;
use PHPUnit\Framework\TestCase;

final class DriverIdTest extends TestCase
{
    public function testFromValidString()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = DriverId::fromString($string);

        static::assertInstanceOf(DriverId::class, $identity);
        static::assertEquals($string, $identity->getId());
        static::assertEquals($string, (string) $identity);
    }

    public function testEquality()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identityOne = DriverId::fromString($string);
        $identityTwo = DriverId::fromString($string);
        $identityThree = DriverId::next();
        static::assertTrue($identityOne->equalTo($identityTwo));
        static::assertFalse($identityTwo->equalTo($identityThree));
    }
}
