<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Clients\Domain\Client\ValueObject\ContractId;
use PHPUnit\Framework\TestCase;

final class ContractIdTest extends TestCase
{
    public function testFromValidString()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ContractId::fromString($string);

        static::assertInstanceOf(ContractId::class, $identity);
        static::assertEquals($string, $identity->getId());
        static::assertEquals($string, (string) $identity);
    }

    public function testEquality()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identityOne = ContractId::fromString($string);
        $identityTwo = ContractId::fromString($string);
        $identityThree = ContractId::next();
        static::assertTrue($identityOne->equalTo($identityTwo));
        static::assertFalse($identityTwo->equalTo($identityThree));
    }
}
