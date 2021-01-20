<?php

namespace Tests\Unit\Clients\Domain\CardOrder;

use App\Application\Domain\ValueObject\IdentityId;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\CardOrder\Order;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\User\UserTest;

final class OrderTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = IdentityId::fromString($string);

        $user = UserTest::createValidEntity();
        $count = 1;
        $name = 'User name';
        $phoneNumber = '+380988765554';

        $result = new Order(
            $identity,
            $user,
            $count,
            $name,
            new Phone($phoneNumber),
            new \DateTimeImmutable()
        );

        $this->assertEquals($user, $result->getUser());
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($phoneNumber, $result->getPhone());
    }

    public static function createValidEntity(): Order
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = IdentityId::fromString($string);

        $user = UserTest::createValidEntity();
        $count = 1;
        $name = 'User name';
        $phoneNumber = '+380988765554';

        return new Order(
            $identity,
            $user,
            $count,
            $name,
            new Phone($phoneNumber),
            new \DateTimeImmutable()
        );
    }
}
