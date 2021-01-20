<?php

namespace App\Feedback\Domain\Feedback\UseCase\Create;

use App\Application\Validator\Constraints as AppAssert;
use App\Clients\Domain\User\User;
use Symfony\Component\Validator\Constraints as Assert;

final class HandlerRequest implements \Domain\Interfaces\HandlerRequest
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=50)
     */
    public $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=64)
     * @AppAssert\Email(mode="strict")
     */
    public $email;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"\App\Feedback\Domain\Feedback\ValueObject\FeedbackCategory", "getNames"})
     */
    public $category;

    /**
     * @var string
     *
     * @Assert\Length(min=2, max=500)
     * @Assert\NotBlank()
     */
    public $comment;

    /**
     * @var User
     */
    public $user;
}
