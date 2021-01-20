<?php
namespace App\Clients\Domain\User\UseCase\Documents;

use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=32)
     */
    public $token;
}
