<?php

namespace App\Clients\Domain\Card\UseCase\DeleteDriver;

use App\Clients\Domain\Card\ValueObject\CardId;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;

class HandlerRequest implements DomainHandlerRequest
{
    private $cardId;

    private $driver;

    public function __construct(CardId $cardId)
    {
        $this->cardId = $cardId;
    }

    public function getCardId(): CardId
    {
        return $this->cardId;
    }
}
