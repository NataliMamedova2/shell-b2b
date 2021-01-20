<?php

namespace App\Clients\Domain\CardOrder\UseCase\Create;

use App\Clients\Domain\User\User;
use Symfony\Component\Validator\Constraints as Assert;

final class HandlerRequest implements \Domain\Interfaces\HandlerRequest
{
    /**
     * @var User
     */
    public $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     * @Assert\Length(max="3")
     */
    public $count;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=164)
     * @Assert\Regex(pattern="/^[a-zA-Z \x22\x27\x60\p{Cyrillic}_-]+$/u")
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=13, min=13)
     * @Assert\Regex(pattern="/^\+[0-9]{0,12}$/")
     */
    public $phone;
}
