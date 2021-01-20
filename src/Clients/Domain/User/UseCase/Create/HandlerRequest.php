<?php

namespace App\Clients\Domain\User\UseCase\Create;

use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use App\Clients\Application\Validator\Constraints\UserExist as UserEntityExist;
use Symfony\Component\Validator\Constraints as Assert;
use App\Clients\Application\Validator\Constraints as ClientsAssert;
use App\Application\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Clients\Domain\Company\Company;

/**
 * @UserEntityExist()
 */
final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var Company
     */
    public $company;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=30)
     * @ClientsAssert\Name()
     */
    public $firstName;

    /**
     * @var string
     *
     * @Assert\Length(min=2, max=30, allowEmptyString=true)
     * @ClientsAssert\Name()
     */
    public $middleName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=30)
     * @ClientsAssert\Name()
     */
    public $lastName;

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
     * @Assert\Length(min=5, max=64)
     * @Assert\Regex(pattern="/^[a-zA-Z\d_.]+$/i")
     */
    public $username;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=6, max=255)
     * @Assert\Regex(pattern="/^\S+$/i")
     */
    public $password;

    /**
     * @var string
     */
    public $repeatPassword;

    /**
     * @var string
     *
     * @Assert\Length(max=13, min=13, allowEmptyString=true)
     * @Assert\Regex(pattern="/^\+[0-9]{0,12}$/")
     */
    public $phone;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"\App\Clients\Domain\User\ValueObject\Role", "getNames"})
     */
    public $role;

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if (!empty($this->repeatPassword) && ($this->password !== $this->repeatPassword)) {
            $context->buildViolation('The password fields must match.')
                ->atPath('password')
                ->addViolation();
        }
    }
}
