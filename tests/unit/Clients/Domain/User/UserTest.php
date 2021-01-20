<?php

namespace Tests\Unit\Clients\Domain\User;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Name;
use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\UserId;
use App\Clients\Domain\User\ValueObject\Username;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\Company\CompanyTest;

final class UserTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = UserId::fromString($string);

        $company = CompanyTest::createValidEntity();

        $email = new Email('test@email.com');
        $username = new Username('test_username');
        $password = 'password';
        $name = new Name('first', 'middle', 'last');
        $role = new Role('ROLE_ADMIN');
        $phone = new Phone('+38907654');

        $entity = User::create(
            $identity,
            $company,
            $email,
            $username,
            $password,
            $name,
            $role,
            $phone
        );

        $this->assertEquals($string, $entity->getId());
        $this->assertEquals($company, $entity->getCompany());
        $this->assertEquals($email, $entity->getEmail());
        $this->assertEquals($username, $entity->getUsername());
        $this->assertEquals($password, $entity->getPassword());
        $this->assertEquals($name, $entity->getName());
        $this->assertEquals([$role->getValue()], $entity->getRoles());
        $this->assertEquals($role->getValue(), $entity->getRole());
        $this->assertEquals(1, $entity->getStatus());
        $this->assertEquals(true, $entity->isActive());
        $this->assertEquals($phone, $entity->getPhone());
    }

    public static function createValidEntity(array $data = []): User
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = UserId::fromString($string);

        $default = [
            'id' => $identity,
        ];

        $data = array_merge($default, $data);

        $company = CompanyTest::createValidEntity();

        $email = new Email('test@email.com');
        $username = new Username('test_username');
        $password = 'password';
        $name = new Name('first', 'middle', 'last');
        $role = new Role('ROLE_ADMIN');
        $phone = new Phone('+38907654');

        return User::create(
            $data['id'],
            $company,
            $email,
            $username,
            $password,
            $name,
            $role,
            $phone
        );
    }
}
