<?php

namespace App\Clients\Domain\Client\UseCase\UpdateProfile;

use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;
use App\Application\Validator\Constraints as AppAssert;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=200)
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=64)
     * @AppAssert\Email(mode="strict")
     */
    public $email;

    /**
     * @Assert\Length(max=64)
     * @AppAssert\Email(mode="strict")
     */
    public $accountingEmail;

    /**
     * @Assert\Length(max=13)
     * @Assert\Regex(pattern="/^\+[0-9]{0,12}$/")
     */
    public $accountingPhone;

    /**
     * @Assert\Length(max=250)
     */
    public $postalAddress;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
