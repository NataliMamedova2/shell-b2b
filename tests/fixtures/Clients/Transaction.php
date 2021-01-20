<?php

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Transaction\Card\Transaction;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcCode;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcName;
use App\Clients\Domain\Transaction\Card\ValueObject\Debit;
use App\Clients\Domain\Transaction\Card\ValueObject\FuelQuantity;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionCode;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionName;
use App\Clients\Domain\Transaction\Card\ValueObject\StellaPrice;
use App\Clients\Domain\Transaction\Card\ValueObject\Transaction1CId;
use App\Clients\Domain\Transaction\Card\ValueObject\TransactionId;
use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

Faker::setLocale('uk_UA');

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(Transaction::class)
    ->setMaker(
        function ($class) {
            /** @var Transaction $class */
            $number = Faker::unixTime()();
            $codePrefix = ['КВ-', 'КВЦ'];
            $code = substr($number, -7);
            $clientId = Faker::randomElement($codePrefix)().substr($number, -7);

            $transaction1CId = new Transaction1CId(Faker::toUpper(substr(Faker::unique()->sha1()(), 0, 32))());
            $clientId = new Client1CId($clientId);
            $cardNumber = new CardNumber(Faker::numberBetween(2101000000, 2102000000)());
            $fuelCode = new FuelCode(Faker::randomElement($codePrefix)().$code);
            $fuelQuantity = new FuelQuantity(Faker::numberBetween(1, 5000000)());
            $stellaPrice = new StellaPrice(Faker::numberBetween(100, 3000)());
            $debit = new Debit(Faker::numberBetween(24, 5000000)());
            $azcCode = new AzcCode(Faker::randomElement($codePrefix)().$code);
            $azcName = new AzcName(Faker::text(10)());
            $regionCode = new RegionCode(Faker::buildingNumber()());
            $regionName = new RegionName(Faker::text(10)());
            $postDate = new \DateTimeImmutable();
            $type = new Type(Faker::randomElement([0, 1, 2])());
            $dateTime = new \DateTimeImmutable();

            return $class::create(
                TransactionId::next(),
                $transaction1CId,
                $clientId,
                $cardNumber,
                $fuelCode,
                $fuelQuantity,
                $stellaPrice,
                $debit,
                $azcCode,
                $azcName,
                $regionCode,
                $regionName,
                $postDate,
                $type,
                $dateTime
            );
        }
    );
