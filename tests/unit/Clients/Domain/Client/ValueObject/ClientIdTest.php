<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Clients\Domain\Client\ValueObject\ClientId;
use PHPUnit\Framework\TestCase;

final class ClientIdTest extends TestCase
{
    public function testFromValidString()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ClientId::fromString($string);

        static::assertInstanceOf(ClientId::class, $identity);
        static::assertEquals($string, $identity->getId());
        static::assertEquals($string, (string) $identity);
    }

    public function testEquality()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identityOne = ClientId::fromString($string);
        $identityTwo = ClientId::fromString($string);
        $identityThree = ClientId::next();
        static::assertTrue($identityOne->equalTo($identityTwo));
        static::assertFalse($identityTwo->equalTo($identityThree));
    }
}
