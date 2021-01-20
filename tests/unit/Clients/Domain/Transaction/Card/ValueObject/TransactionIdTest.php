<?php

namespace Tests\Unit\Clients\Domain\Transaction\Card\ValueObject;

use App\Clients\Domain\Transaction\Card\ValueObject\TransactionId;
use PHPUnit\Framework\TestCase;

final class TransactionIdTest extends TestCase
{
    public function testFromValidString()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = TransactionId::fromString($string);

        static::assertInstanceOf(TransactionId::class, $identity);
        static::assertEquals($string, $identity->getId());
        static::assertEquals($string, (string) $identity);
    }

    public function testEquality()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identityOne = TransactionId::fromString($string);
        $identityTwo = TransactionId::fromString($string);
        $identityThree = TransactionId::next();
        static::assertTrue($identityOne->equalTo($identityTwo));
        static::assertFalse($identityTwo->equalTo($identityThree));
    }
}
