<?php

namespace App\Clients\Domain\Card\UseCase\AddStopList;

use App\Clients\Domain\Card\Card;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var Card
     */
    public $card;
}
