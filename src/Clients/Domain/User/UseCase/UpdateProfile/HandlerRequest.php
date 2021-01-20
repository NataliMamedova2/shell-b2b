<?php

namespace App\Clients\Domain\User\UseCase\UpdateProfile;

use App\Application\Validator\Constraints as AppAssert;
use App\Clients\Application\Validator\Constraints as ClientsAssert;
use App\Clients\Application\Validator\Constraints\UserExist as UserEntityExist;
use App\Clients\Domain\User\User;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @UserEntityExist()
 */
final class HandlerRequest implements DomainHandlerRequest
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getId(): string
    {
        return $this->user->getId();
    }

    /**
     * @var User
     *
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=30)
     * @ClientsAssert\Name()
     */
    public $firstName;

    /**
     * @Assert\Length(min=2, max=30, allowEmptyString=true)
     * @ClientsAssert\Name()
     */
    public $middleName;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=30)
     * @ClientsAssert\Name()
     */
    public $lastName;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=64)
     * @AppAssert\Email(mode="strict")
     */
    public $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=64)
     * @Assert\Regex(pattern="/^[a-zA-Z\d_.]+$/i")
     */
    public $username;

    /**
     * @Assert\Length(min=6, max=255, allowEmptyString=true)
     * @Assert\Regex(pattern="/^\S+$/i")
     */
    public $password;

    /**
     * @var string
     */
    public $repeatPassword;

    /**
     * @Assert\Length(max=13, min=13, allowEmptyString=true)
     * @Assert\Regex(pattern="/^\+[0-9]{0,12}$/")
     */
    public $phone;

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if (!empty($this->repeatPassword) && ($this->password !== $this->repeatPassword)) {
            $context->buildViolation('The password fields must match.')
                ->atPath('password')
                ->addViolation();
        }
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
