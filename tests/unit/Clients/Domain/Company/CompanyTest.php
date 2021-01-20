<?php

namespace Tests\Unit\Clients\Domain\Company;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\Company\ValueObject\Accounting;
use App\Clients\Domain\Company\ValueObject\CompanyId;
use App\Clients\Domain\Company\ValueObject\Name;
use App\Clients\Domain\Company\ValueObject\PostalAddress;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\Client\ClientTest;
use Tests\Unit\Users\Domain\User\UserTest;

final class CompanyTest extends TestCase
{
    public function testRegister(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = CompanyId::fromString($string);

        $client = ClientTest::createValidEntity();
        $email = new Email('test@email.com');

        $entity = Company::register(
            $identity,
            $client,
            $email,
            new \DateTimeImmutable('2019-10-10')
        );

        $this->assertEquals($string, $entity->getId());
        $this->assertEquals($client, $entity->getClient());
        $this->assertEquals($client->getFullName(), $entity->getName());
        $this->assertEquals($email, $entity->getEmail());
    }

    public function testUpdate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = CompanyId::fromString($string);

        $client = ClientTest::createValidEntity();
        $email = new Email('test@email.com');

        $entity = Company::register(
            $identity,
            $client,
            $email,
            new \DateTimeImmutable('2019-10-10')
        );

        $newName = new Name('New Company name');
        $accounting = new Accounting(new Email('test@email.com'), new Phone('+323456788'));
        $postalAddress = new PostalAddress('address');

        $entity->update(
            $newName,
            $accounting,
            $postalAddress
        );

        $this->assertEquals($string, $entity->getId());
        $this->assertEquals($client, $entity->getClient());
        $this->assertEquals($email, $entity->getEmail());
        $this->assertEquals($newName, $entity->getName());
        $this->assertEquals($accounting, $entity->getAccounting());
        $this->assertEquals($postalAddress, $entity->getPostalAddress());
    }

    public static function createValidEntity(): Company
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = CompanyId::fromString($string);

        $client = ClientTest::createValidEntity();
        $email = new Email('test@email.com');

        $entity = Company::register(
            $identity,
            $client,
            $email,
            new \DateTimeImmutable()
        );

        $name = new Name('New Company name');
        $accounting = new Accounting(new Email('test@email.com'), new Phone('+323456788'));
        $postalAddress = new PostalAddress('address');
        $entity->update(
            $name,
            $accounting,
            $postalAddress
        );

        return $entity;
    }
}
