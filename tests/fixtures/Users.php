<?php

use App\Application\Domain\ValueObject as BaseValueObject;
use App\Users\Domain\User\ValueObject as UserValueObject;
use App\Users\Domain\User\User;
use App\Users\Domain\User\ValueObject\UserId;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

Faker::setLocale('uk_UA');

$i = 0;
/* @var FactoryMuffin $fm */
$fm
    ->define(User::class)
    ->setMaker(function ($class) use ($i) {
        ++$i;
        $entity = $class::create(
            UserId::next(),
            new BaseValueObject\Email(Faker::email()()),
            new UserValueObject\Username(Faker::userName()().'_'.$i),
            new UserValueObject\FullName(Faker::name()()),
            new UserValueObject\Role(Faker::randomElement(array_flip(UserValueObject\Role::getNames()))()),
            new UserValueObject\Status(Faker::randomElement(array_flip(UserValueObject\Status::getNames()))()),
            new UserValueObject\Phone('+30898887665'),
            new UserValueObject\Avatar('tests/', 'chess.jpg', ['x' => '0', 'y' => '0', 'width' => '200', 'height' => '200'])
        );
        $password = '$argon2id$v=19$m=65536,t=4,p=1$j6mHiXd8jNMwAKPNuNb0oA$gei3ZhxmdyxDSMijBCohh7kbeKIpHpruyDIVWOxssao'; // 111
        $entity->changePassword($password);

        return $entity;
    });
