<?php

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Price\Price;
use App\Clients\Domain\Fuel\Price\ValueObject\FuelPrice;
use App\Clients\Domain\Fuel\Price\ValueObject\PriceId;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(Price::class)
    ->setMaker(function ($class) {
        /** @var Price $class */
        $number = Faker::unixTime()();
        $code = substr($number, -7);
        $codePrefix = 'КВЦ';

        return $class::create(
            PriceId::next(),
            new FuelCode($codePrefix.$code),
            new FuelPrice(Faker::numberBetween(1500, 4000)()),
            new DateTimeImmutable()
        );
    });
