<?php

namespace Tests\Unit\Api\Resource;

use App\Api\Resource\DriverShort;
use App\Clients\Domain\Driver\CarNumber;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\ValueObject\Name;
use PHPUnit\Framework\TestCase;

final class DriverSearchTest extends TestCase
{
    public function testPrepare()
    {
        $driverModelMock = $this->prophesize(Driver::class);

        $name = new Name('First', 'Middle', 'Last');
        $driverModelMock->getName()
            ->shouldBeCalled()
            ->willReturn($name);

        $string = '550e8400-e29b-41d4-a716-446655440000';
        $driverModelMock->getId()
            ->shouldBeCalled()
            ->willReturn($string);

        $carNumberMock = $this->prophesize(CarNumber::class);
        $carNumbers = [
            $carNumberMock->reveal(),
        ];
        $driverModelMock->getCarNumbers()
            ->shouldBeCalled()
            ->willReturn($carNumbers);

        $carNumber = 'AA09344';
        $carNumberMock->getNumber()
            ->shouldBeCalled()
            ->willReturn($carNumber);

        $resource = new DriverShort();

        $result = $resource->prepare($driverModelMock->reveal());

        $this->assertEquals($string, $result->id);
        $this->assertEquals($name->getLastName(), $result->lastName);
        $this->assertEquals($name->getMiddleName(), $result->middleName);
        $this->assertEquals($name->getFirstName(), $result->firstName);
        $this->assertEquals([['number' => $carNumber]], $result->carsNumbers);
    }
}
