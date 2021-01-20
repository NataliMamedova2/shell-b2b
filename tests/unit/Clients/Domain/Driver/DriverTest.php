<?php

namespace Tests\Unit\Clients\Domain\Driver;

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\ValueObject\CarNumber;
use App\Clients\Domain\Driver\ValueObject\DriverId;
use App\Clients\Domain\Driver\ValueObject\Name;
use App\Clients\Domain\Driver\ValueObject\Note;
use App\Clients\Domain\Driver\ValueObject\Status;
use Domain\Exception\DomainException;
use PHPUnit\Framework\TestCase;

final class DriverTest extends TestCase
{
    public function testCreate()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = DriverId::fromString($string);

        $firstName = 'firstName';
        $middleName = 'middleName';
        $lastName = 'lastName';

        $emailValue = 'email@email.com';

        $phones = [
            '+380972342344',
            '+380632342300',
        ];

        $noteValue = 'note text';
        $dateTime = new \DateTimeImmutable('2020-01-01');

        $clientMock = $this->prophesize(Client::class);
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn('clientId');

        $entity = Driver::create(
            $identity,
            $clientMock->reveal(),
            new Name($firstName, $middleName, $lastName),
            Status::active(),
            $phones,
            $dateTime,
            new Email($emailValue),
            new Note($noteValue)
        );

        $this->assertEquals($string, $entity->getId());
        $this->assertEquals($emailValue, $entity->getEmail());
        $this->assertEquals($firstName, $entity->getName()->getFirstName());
        $this->assertEquals($middleName, $entity->getName()->getMiddleName());
        $this->assertEquals($lastName, $entity->getName()->getLastName());

        $this->assertCount(2, $entity->getPhones());
        $this->assertCount(0, $entity->getCarNumbers());
    }

    public function testCreateEmptyPhonesReturnException()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = DriverId::fromString($string);

        $firstName = 'firstName';
        $middleName = 'middleName';
        $lastName = 'lastName';

        $emailValue = 'email@email.com';

        $phones = [];

        $noteValue = 'note text';
        $dateTime = new \DateTimeImmutable('2020-01-01');

        $clientMock = $this->prophesize(Client::class);
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn('clientId');

        $this->expectException(DomainException::class);

        Driver::create(
            $identity,
            $clientMock->reveal(),
            new Name($firstName, $middleName, $lastName),
            Status::active(),
            $phones,
            $dateTime,
            new Email($emailValue),
            new Note($noteValue)
        );
    }

    public function testUpdateEmptyPhonesReturnException()
    {
        $entity = $this->createDriver();

        $firstName = 'newFirstName';
        $middleName = 'newMiddleName';
        $lastName = 'newLastName';

        $emailValue = 'newemail@email.com';
        $noteValue = 'new text';
        $status = Status::active();

        $phones = [];
        $carNumbers = [];

        $this->expectException(DomainException::class);

        $entity->update(
            new Name($firstName, $middleName, $lastName),
            $status,
            $phones,
            $carNumbers,
            new \DateTimeImmutable('2000-01-01 00:00:01'),
            new Email($emailValue),
            new Note($noteValue)
        );
    }

    public function testUpdateReturnEntity()
    {
        $entity = $this->createDriver();

        $firstName = 'newFirstName';
        $middleName = 'newMiddleName';
        $lastName = 'newLastName';

        $emailValue = 'newemail@email.com';
        $noteValue = 'new text';
        $status = Status::active();

        $phones = ['+380987655433'];
        $carNumbers = ['AA12333'];

        $result = $entity->update(
            new Name($firstName, $middleName, $lastName),
            $status,
            $phones,
            $carNumbers,
            new \DateTimeImmutable('2000-01-01 00:00:01'),
            new Email($emailValue),
            new Note($noteValue)
        );

        $this->assertCount(1, $result->getPhones());
        $this->assertCount(1, $result->getCarNumbers());
        $this->assertEquals($firstName, $result->getName()->getFirstName());
    }

    public function testAddCarNumberNumberAlreadyExistReturnException()
    {
        $entity = $this->createDriver();

        $dateTime = new \DateTimeImmutable('2020-01-01');
        $numberValue = 'AI1233211';
        $entity->addCarNumber(new CarNumber($numberValue), $dateTime);

        $numberValue_2 = 'OO1233222';
        $entity->addCarNumber(new CarNumber($numberValue_2), $dateTime);

        $this->expectException(DomainException::class);

        $entity->addCarNumber(new CarNumber($numberValue), new \DateTimeImmutable('2020-12-23'));
    }

    public function testGetCarNumbersReturnArrayOfObjects()
    {
        $entity = $this->createDriver();

        $dateTime = new \DateTimeImmutable('2020-01-01');
        $numberValue = 'AI1233211';
        $entity->addCarNumber(new CarNumber($numberValue), $dateTime);

        $numberValue_2 = 'OO1233222';
        $entity->addCarNumber(new CarNumber($numberValue_2), $dateTime);

        $this->assertCount(2, $entity->getCarNumbers());
    }

    private function createDriver(): Driver
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = DriverId::fromString($string);

        $firstName = 'firstName';
        $middleName = 'middleName';
        $lastName = 'lastName';

        $emailValue = 'email@email.com';

        $phones = ['+380632342300'];

        $noteValue = 'note text';
        $dateTime = new \DateTimeImmutable('2020-01-01');

        $clientMock = $this->prophesize(Client::class);
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn('clientId');

        return Driver::create(
            $identity,
            $clientMock->reveal(),
            new Name($firstName, $middleName, $lastName),
            Status::active(),
            $phones,
            $dateTime,
            new Email($emailValue),
            new Note($noteValue)
        );
    }
}
