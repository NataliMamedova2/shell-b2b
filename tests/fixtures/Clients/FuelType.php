<?php

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Fuel\Type\ValueObject\PurseCode;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelName;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelPurse;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\Fuel\Type\ValueObject\TypeId;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

Faker::setLocale('uk_UA');

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(Type::class)
    ->setMaker(function ($class) {
        /** @var Type $class */
        $number = Faker::unixTime()();
        $code = substr($number, -7);
        $codePrefix = ['КВ-', 'КВЦ'];

        return $class::create(
            TypeId::next(),
            new FuelCode(Faker::randomElement($codePrefix)().$code),
            new FuelName(Faker::text(60)()),
            new FuelPurse(Faker::randomElement([0, 1])()),
            new FuelType(Faker::randomElement([1, 2, 3])()),
            new PurseCode(Faker::numberBetween(0, 52)()),
            new DateTimeImmutable()
        );
    });
