<?php

namespace Tests\Unit\Clients\Domain\RefillBalance\ValueObject;

use App\Clients\Domain\RefillBalance\ValueObject\RefillBalanceId;
use PHPUnit\Framework\TestCase;

final class RefillBalanceIdTest extends TestCase
{
    public function testEquality()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identityOne = RefillBalanceId::fromString($string);
        $identityTwo = RefillBalanceId::fromString($string);

        $identityThree = RefillBalanceId::next();
        static::assertTrue($identityOne->equalTo($identityTwo));
        static::assertFalse($identityTwo->equalTo($identityThree));
    }

    public function testFromValidString()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = RefillBalanceId::fromString($string);

        static::assertInstanceOf(RefillBalanceId::class, $identity);
        static::assertEquals($string, $identity->getId());
        static::assertEquals($string, (string) $identity);
    }
}
