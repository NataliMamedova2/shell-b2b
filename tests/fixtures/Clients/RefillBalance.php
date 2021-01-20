<?php

use App\Clients\Domain\RefillBalance\RefillBalance;
use App\Clients\Domain\RefillBalance\ValueObject\Amount;
use App\Clients\Domain\RefillBalance\ValueObject\CardOwner;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\RefillBalance\ValueObject\Operation;
use App\Clients\Domain\RefillBalance\ValueObject\OperationDate;
use App\Clients\Domain\RefillBalance\ValueObject\RefillBalanceId;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(RefillBalance::class)
    ->setMaker(function ($class) {
        /* @var RefillBalance $class */

        $operationDate = new \DateTimeImmutable();
        $operationTime = new \DateTimeImmutable('17:53:16');
        $dateTime = new OperationDate($operationDate, $operationTime);

        return new $class(
            RefillBalanceId::next(),
            new CardOwner(2),
            new FcCbrId(Faker::unique()->numberBetween()()),
            new Operation(Faker::randomElement([0, 1])()),
            new Amount(Faker::numberBetween()()),
            $dateTime
        );
    });
