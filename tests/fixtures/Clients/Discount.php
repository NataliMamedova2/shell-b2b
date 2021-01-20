<?php

use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Discount\Discount;
use App\Clients\Domain\Discount\ValueObject\DiscountId;
use App\Clients\Domain\Discount\ValueObject\DiscountSum;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(Discount::class)
    ->setMaker(
        function ($class) use ($fm) {
            /* @var Discount $class */

            /** @var Client $client */
            $client = $fm->instance(Client::class);

            return $class::create(
                DiscountId::next(),
                new Client1CId($client->getClient1CId()),
                new DiscountSum(Faker::numberBetween(1500, 4000)()),
                new \DateTimeImmutable(),
                new DateTimeImmutable()
            );
        }
    );
