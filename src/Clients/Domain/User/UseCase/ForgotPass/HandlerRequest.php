<?php

namespace App\Clients\Domain\User\UseCase\ForgotPass;

use App\Clients\Application\Validator\Constraints\UserNotExist;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UserNotExist()
 */
final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     */
    public $username;
}
