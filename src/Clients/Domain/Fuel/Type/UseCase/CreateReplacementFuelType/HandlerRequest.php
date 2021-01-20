<?php

namespace App\Clients\Domain\Fuel\Type\UseCase\CreateReplacementFuelType;

use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use App\Clients\Application\Validator\Constraints\UserExist as UserEntityExist;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UserEntityExist()
 */
final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=8, max=16)
     */
    public $fuelCode;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=8, max=16)
     */
    public $fuelReplacementCode;
}
