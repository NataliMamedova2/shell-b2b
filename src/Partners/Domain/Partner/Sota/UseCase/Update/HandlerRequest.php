<?php
namespace App\Partners\Domain\Partner\Sota\UseCase\Update;

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
