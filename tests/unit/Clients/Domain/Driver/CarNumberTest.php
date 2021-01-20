<?php

namespace Tests\Unit\Clients\Domain\Driver;

use App\Clients\Domain\Driver\CarNumber;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\ValueObject\CarNumberId;
use PHPUnit\Framework\TestCase;

final class CarNumberTest extends TestCase
{
    public function testCreate()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = CarNumberId::fromString($string);

        $dateTime = new \DateTimeImmutable('2020-01-01');

        $driverMock = $this->prophesize(Driver::class);
        $numberValue = 'AI1233288';
        $number = new \App\Clients\Domain\Driver\ValueObject\CarNumber($numberValue);

        $entity = new CarNumber(
            $identity,
            $driverMock->reveal(),
            $number,
            $dateTime
        );

        $this->assertEquals($numberValue, $entity->getNumber());
    }
}
