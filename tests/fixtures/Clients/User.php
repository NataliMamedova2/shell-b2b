<?php

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject as UserValueObject;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

Faker::setLocale('uk_UA');

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(User::class)
    ->setMaker(function ($class) use ($fm) {
        /** @var Company $company */
        $company = $fm->create(Company::class);
        $password = '$argon2id$v=19$m=65536,t=4,p=1$j6mHiXd8jNMwAKPNuNb0oA$gei3ZhxmdyxDSMijBCohh7kbeKIpHpruyDIVWOxssao';
        $roles = array_keys(UserValueObject\Role::getNames());

        $username = Faker::unique()->userName()();
        if (strlen($username) < 5) {
            $username = $username.str_repeat('0', 5 - strlen($username));
        }

        /* @var User $class */
        return $class::create(
            UserValueObject\UserId::next(),
            $company,
            new Email(Faker::unique()->email()()),
            new UserValueObject\Username($username),
            $password,
            new UserValueObject\Name(Faker::firstName()(), Faker::firstName()(), Faker::lastName()()),
            new UserValueObject\Role(Faker::randomElement($roles)())
        );
    });
