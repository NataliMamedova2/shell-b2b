<?php

namespace Tests\Unit\Users\Domain\User;

use App\Application\Domain\ValueObject\Email;
use App\Users\Domain\User\User;
use App\Users\Domain\User\ValueObject\Avatar;
use App\Users\Domain\User\ValueObject\FullName;
use App\Users\Domain\User\ValueObject\Phone;
use App\Users\Domain\User\ValueObject\Role;
use App\Users\Domain\User\ValueObject\Status;
use App\Users\Domain\User\ValueObject\UserId;
use App\Users\Domain\User\ValueObject\Username;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

    public function testCreate(): void
    {
        $name = new FullName('John Dou');
        $password = '123455';
        $textEmail = 'example@mail.com';
        $textUsername = 'username';

        $role = new Role('ROLE_ADMIN');
        $statusValue = 1;
        $status = new Status($statusValue);

        $phone = new Phone('12344567876');
        $avatar = new Avatar('path/to', 'filename.jpeg');

        $id = UserId::next();
        $entity = User::create(
            $id,
            new Email($textEmail),
            new Username($textUsername),
            $name,
            $role,
            $status,
            $phone,
            $avatar
        );
        $entity->changePassword($password);

        $this->assertEquals($id, $entity->getId());
        $this->assertEquals($name, $entity->getName());
        $this->assertEquals($textEmail, $entity->getEmail());
        $this->assertEquals($textUsername, $entity->getUsername());
        $this->assertTrue(in_array($role, $entity->getRoles()));
        $this->assertEquals($statusValue, $entity->getStatus());
        $this->assertEquals($phone, $entity->getPhone());
        $this->assertEquals($avatar, $entity->getAvatar());
    }

    public static function createValidEntity(array $data = []): User
    {
        $default = [
            'status' => 1,
        ];

        $data = array_merge($default, $data);

        $name = new FullName('John Dou');
        $password = '123455';
        $textEmail = 'example@mail.com';
        $textUsername = 'username';

        $role = new Role('ROLE_ADMIN');
        $status = new Status($data['status']);

        $phone = new Phone('12344567876');
        $avatar = new Avatar('path/to', 'filename.jpeg');

        $id = UserId::next();
        $entity = User::create(
            $id,
            new Email($textEmail),
            new Username($textUsername),
            $name,
            $role,
            $status,
            $phone,
            $avatar
        );
        $entity->changePassword($password);

        return $entity;
    }
}
