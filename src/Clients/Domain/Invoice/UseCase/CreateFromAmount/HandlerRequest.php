<?php

namespace App\Clients\Domain\Invoice\UseCase\CreateFromAmount;

use App\Clients\Application\Validator\Constraints\ClientExist as ClientEntityExist;
use App\Clients\Domain\Client\Client;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var Client
     *
     * @Assert\NotBlank()
     * @ClientEntityExist()
     */
    public $client;

    /**
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="1000000")
     */
    public $amount;
}
