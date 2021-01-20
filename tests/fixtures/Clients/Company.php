<?php

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\Company\ValueObject as CompanyValueObject;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

Faker::setLocale('uk_UA');

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(Company::class)
    ->setMaker(function ($class) use ($fm) {
        /** @var Client $client */
        $client = $fm->create(Client::class);

        /* @var Company $class */
        return $class::register(
            CompanyValueObject\CompanyId::next(),
            $client,
            new Email(Faker::email()()),
            new DateTimeImmutable()
        );
    });
