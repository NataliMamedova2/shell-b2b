<?php

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\FuelLimit\FuelLimit;
use App\Clients\Domain\FuelLimit\ValueObject\DayLimit;
use App\Clients\Domain\FuelLimit\ValueObject\FuelId;
use App\Clients\Domain\FuelLimit\ValueObject\MonthLimit;
use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use App\Clients\Domain\FuelLimit\ValueObject\WeekLimit;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

Faker::setLocale('uk_UA');

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(FuelLimit::class)
    ->setMaker(
        function ($class) {
            /** @var FuelLimit $class */
            $number = Faker::unixTime()();
            $codePrefix = ['КВ-', 'КВЦ'];
            $code = substr($number, -7);
            $clientId = Faker::randomElement($codePrefix)().substr($number, -7);

            $clientId = new Client1CId($clientId);
            $cardNumber = new CardNumber(Faker::numberBetween(2101000000, 2102000000)());
            $fuelCode = new FuelCode(Faker::randomElement($codePrefix)().$code);
            $dayLimit = new DayLimit(Faker::numberBetween(100, 1000000)());
            $weekLimit = new WeekLimit(Faker::numberBetween(100, 9000000)());
            $monthLimit = new MonthLimit(Faker::numberBetween(100, 3000000)());
            $purseActivity = PurseActivity::active();
            $dateTime = new \DateTimeImmutable();

            return $class::create(
                FuelId::next(),
                $clientId,
                $cardNumber,
                $fuelCode,
                $dayLimit,
                $weekLimit,
                $monthLimit,
                $purseActivity,
                $dateTime
            );
        }
    );
