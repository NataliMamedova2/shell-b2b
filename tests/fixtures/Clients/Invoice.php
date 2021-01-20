<?php

use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Invoice\Invoice;
use App\Clients\Domain\Invoice\ValueObject\Date;
use App\Clients\Domain\Invoice\ValueObject\InvoiceNumber;
use App\Clients\Domain\Invoice\ValueObject\ValueTax;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(Invoice::class)
    ->setMaker(
        function ($class) use ($fm) {
            /* @var Invoice $class */

            /** @var Client $client */
            $client = $fm->instance(Client::class);

            $dateTime = new \DateTimeImmutable();

            return $class::create(
                IdentityId::next(),
                $client->getClient1CId(),
                new InvoiceNumber(Faker::unixTime()()),
                new ValueTax(20),
                new Date($dateTime, 3)
            );
        }
    );
