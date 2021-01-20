<?php

use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Domain\ClientInfo\ValueObject\Balance;
use App\Clients\Domain\ClientInfo\ValueObject\ClientPcId;
use App\Clients\Domain\ClientInfo\ValueObject\CreditLimit;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\ClientInfo\ValueObject\LastTransactionDate;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(ClientInfo::class)
    ->setMaker(function ($class) {
        /** @var ClientInfo $class */
        $lastTransactionDate = new LastTransactionDate(
            \DateTimeImmutable::createFromFormat('d/m/Y', '01/01/1900'),
            new \DateTimeImmutable('00:00:00')
        );

        return $class::create(
            IdentityId::next(),
            new ClientPcId(Faker::unique()->numberBetween()()),
            new FcCbrId(Faker::unique()->numberBetween()()),
            new Balance(Faker::numberBetween()()),
            new CreditLimit(Faker::numberBetween()()),
            $lastTransactionDate,
            new DateTimeImmutable()
        );
    });
