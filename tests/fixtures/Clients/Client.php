<?php

use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Client\ValueObject as ClientValueObject;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

Faker::setLocale('uk_UA');

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(Client::class)
    ->setMaker(function ($class) {
        /** @var Client $class */
        $number = Faker::unixTime()();
        $edrpouInn = '24584810';
        $clientId = substr($number, -7);
        $managerId = substr($number, -7);

        return $class::createWithContract(
            ClientValueObject\ClientId::next(),
            new Client1CId('КВ-'.$clientId),
            new ClientValueObject\FullName(Faker::name()()),
            new ClientValueObject\EdrpouInn($edrpouInn),
            new ClientValueObject\Type(0),
            new ClientValueObject\NktId(Faker::numberBetween()()),
            new ClientValueObject\Manager1CId('КВЦ'.$managerId),
            new ClientValueObject\Agent1CId(''),
            new FcCbrId(12435),
            new ClientValueObject\Status(1),
            new ClientValueObject\ContractNumber(Faker::randomNumber(5)()),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );
    });
