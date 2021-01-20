<?php

namespace App\Users\Domain\User\UseCase\Update;

use App\Application\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;
use App\Users\Application\Validator\Constraints\UserExist as UserEntityExist;
use App\Users\Application\Validator\Constraints\ManagerIdExist;
use App\Media\Validator\Constraints\Media as AssertMedia;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @UserEntityExist()
 * @ManagerIdExist()
 */
final class HandlerRequest implements \Domain\Interfaces\HandlerRequest
{
    /**
     * @var string
     */
    private $id;

    /**
     * @Assert\Length(max=12)
     */
    public $manager1CId;

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
     * @Assert\Length(min=6, max=255, allowEmptyString=true)
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
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     * @Assert\Regex(pattern="/^[a-zA-Z \x22\x27\x60\p{Cyrillic}_-]+$/u")
     */
    public $name;

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

    /**
     * @var string
     */
    public $role;

    /**
     * @var string
     */
    public $status;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

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
        if (!empty($this->password) && $this->password !== $this->repeatPassword) {
            $context->buildViolation('The password fields must match.')
                ->atPath('password')
                ->addViolation();
        }
    }
}
