<?php

namespace App\Clients\Domain\Card\UseCase\ChangeDriver;

use App\Clients\Domain\Card\ValueObject\CardId;
use App\Clients\Domain\Driver\Driver;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;

final class HandlerRequest implements DomainHandlerRequest
{
    private $cardId;

    private $driver;

    public function __construct(CardId $cardId, Driver $driver)
    {
        $this->cardId = $cardId;
        $this->driver = $driver;
    }

    public function getCardId(): CardId
    {
        return $this->cardId;
    }

    public function getDriver(): Driver
    {
        return $this->driver;
    }
}
