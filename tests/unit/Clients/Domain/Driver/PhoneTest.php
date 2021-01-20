<?php

namespace Tests\Unit\Clients\Domain\Driver;

use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\Phone;
use App\Clients\Domain\Driver\ValueObject\PhoneId;
use App\Clients\Domain\Driver\ValueObject\PhoneNumber;
use PHPUnit\Framework\TestCase;

final class PhoneTest extends TestCase
{
    public function testCreate()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = PhoneId::fromString($string);

        $dateTime = new \DateTimeImmutable('2020-01-01');

        $driverMock = $this->prophesize(Driver::class);
        $numberValue = '+380972342344';
        $number = new PhoneNumber($numberValue);

        $entity = new Phone(
            $identity,
            $driverMock->reveal(),
            $number,
            $dateTime
        );

        $this->assertEquals($numberValue, $entity->getNumber());
    }
}
