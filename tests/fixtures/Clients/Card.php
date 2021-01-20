<?php

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\ValueObject\CardId;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Card\ValueObject\CarNumber;
use App\Clients\Domain\Card\ValueObject\DayLimit;
use App\Clients\Domain\Card\ValueObject\MonthLimit;
use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use App\Clients\Domain\Card\ValueObject\TimeUse;
use App\Clients\Domain\Card\ValueObject\WeekLimit;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(Card::class)
    ->setMaker(function ($class) {
        /** @var Card $class */
        $number = Faker::unixTime()();
        $codePrefix = ['КВ-', 'КВЦ'];
        $clientId = Faker::randomElement($codePrefix)().substr($number, -7);

        $clientId = new Client1CId($clientId);
        $cardNumber = new CardNumber(Faker::numberBetween(2101000000, 2102000000)());
        $carNumber = new CarNumber('');
        $dayLimit = new DayLimit(Faker::numberBetween(100, 1000000000)());
        $weekLimit = new WeekLimit(Faker::numberBetween(100, 1000000000000)());
        $monthLimit = new MonthLimit(Faker::numberBetween(-1294967296, 10000000000000)());
        $serviceSchedule = new ServiceSchedule('1010110');

        $timeUse = new TimeUse(new \DateTimeImmutable('00:00'), new \DateTimeImmutable('23:00'));
        $cardStatus = new CardStatus(Faker::randomElement([0, 1])());
        $dateTime = new \DateTimeImmutable();

        return $class::create(
            CardId::next(),
            $clientId,
            $cardNumber,
            $carNumber,
            $dayLimit,
            $weekLimit,
            $monthLimit,
            $serviceSchedule,
            $timeUse,
            $cardStatus,
            $dateTime
        );
    });
