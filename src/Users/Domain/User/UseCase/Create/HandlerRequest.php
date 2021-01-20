<?php

namespace App\Users\Domain\User\UseCase\Create;

use App\Application\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;
use App\Users\Application\Validator\Constraints\UserExist as UserEntityExist;
use App\Media\Validator\Constraints\Media as AssertMedia;
use App\Users\Application\Validator\Constraints\ManagerIdExist;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @UserEntityExist()
 * @ManagerIdExist()
 */
final class HandlerRequest implements \Domain\Interfaces\HandlerRequest
{
    /**
     * @Assert\Length(max=12)
     */
    public $manager1CId;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     * @Assert\Regex(pattern="/^[a-zA-Z \x22\x27\x60\p{Cyrillic}_-]+$/u")
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
     * @Assert\NotBlank()
     * @Assert\Length(min=4, max=255)
     * @Assert\Regex(pattern="/^[a-z\d_.]+$/i")
     */
    public $username;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=6, max=255)
     */
    public $password;

    /**
     * @var string
     */
    public $repeatPassword;

    /**
     * @var string
     * @Assert\Length(max=13)
     * @Assert\Regex(pattern="/^\+[0-9]{0,12}$/")
     */
    public $phone;

    /**
     * @AssertMedia()
     */
    public $avatar;

    public $role;

    public $status;

    public function extract(array $properties = []): array
    {
        $state = get_object_vars($this);

        foreach (array_keys($state) as $property) {
            $getter = 'get'.ucfirst($property);
            if (method_exists($this, $getter)) {
                $state[$property] = $this->{$getter}();
            }
        }

        if (empty($properties)) {
            return $state;
        }

        $rawArray = array_fill_keys($properties, null);

        return array_intersect_key($state, $rawArray);
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->password !== $this->repeatPassword) {
            $context->buildViolation('The password fields must match.')
                ->atPath('password')
                ->addViolation();
        }
    }
}
