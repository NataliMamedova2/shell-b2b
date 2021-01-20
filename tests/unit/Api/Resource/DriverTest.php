<?php

namespace Tests\Unit\Api\Resource;

use App\Api\Resource\Driver;
use App\Clients\Domain\Driver\Phone;
use App\Clients\Domain\Driver\ValueObject\Name;
use PHPUnit\Framework\TestCase;

final class DriverTest extends TestCase
{
    public function testPrepare()
    {
        $driverModelMock = $this->prophesize(\App\Clients\Domain\Driver\Driver::class);

        $name = new Name('First', 'Middle', 'Last');
        $driverModelMock->getName()
            ->shouldBeCalled()
            ->willReturn($name);

        $string = '550e8400-e29b-41d4-a716-446655440000';
        $driverModelMock->getId()
            ->shouldBeCalled()
            ->willReturn($string);

        $emailValue = '550e8400@email.com';
        $driverModelMock->getEmail()
            ->shouldBeCalled()
            ->willReturn($emailValue);

        $noteValue = 'Note text';
        $driverModelMock->getNote()
            ->shouldBeCalled()
            ->willReturn($noteValue);

        $statusActiveValue = 1;
        $statusActiveName = 'active';
        $driverModelMock->getStatus()
            ->shouldBeCalled()
            ->willReturn($statusActiveValue);

        $phoneMock = $this->prophesize(Phone::class);
        $phones = [
            $phoneMock->reveal(),
        ];
        $driverModelMock->getPhones()
            ->shouldBeCalled()
            ->willReturn($phones);

        $phoneNumber = '+380972342344';
        $phoneMock->getNumber()
            ->shouldBeCalled()
            ->willReturn($phoneNumber);

        $carNumbers = [];
        $driverModelMock->getCarNumbers()
            ->shouldBeCalled()
            ->willReturn($carNumbers);

        $resource = new Driver();

        $result = $resource->prepare($driverModelMock->reveal());

        $this->assertEquals($string, $result->id);
        $this->assertEquals($name->getLastName(), $result->lastName);
        $this->assertEquals($name->getMiddleName(), $result->middleName);
        $this->assertEquals($name->getFirstName(), $result->firstName);
        $this->assertEquals($statusActiveName, $result->status);
        $this->assertEquals($noteValue, $result->note);
        $this->assertEquals($emailValue, $result->email);
        $this->assertEquals([['number' => $phoneNumber]], $result->phones);
        $this->assertEquals([], $result->carsNumbers);
    }
}
