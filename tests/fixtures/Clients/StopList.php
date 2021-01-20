<?php

use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\StopList;
use League\FactoryMuffin\FactoryMuffin;

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(StopList::class)
    ->setMaker(function ($class) use ($fm) {
        /** @var Card $card */
        $card = $fm->instance(Card::class);

        /* @var StopList $class */
        return new $class(
            $card,
            new DateTimeImmutable()
        );
    });
