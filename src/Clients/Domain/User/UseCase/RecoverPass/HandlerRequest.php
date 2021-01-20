<?php

namespace App\Clients\Domain\User\UseCase\RecoverPass;

use App\Clients\Application\Validator\Constraints\UserNotExist;
use App\Clients\Domain\User\ValueObject\Token;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var string
     */
    public $token;

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
