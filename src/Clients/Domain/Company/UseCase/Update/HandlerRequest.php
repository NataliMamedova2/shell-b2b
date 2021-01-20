<?php

namespace App\Clients\Domain\Company\UseCase\Update;

use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;
use App\Application\Validator\Constraints as AppAssert;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @Assert\NotBlank()
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=200)
     */
    public $name;

    /**
     * @Assert\Length(min=5, max=64, allowEmptyString=true)
     * @AppAssert\Email(mode="strict")
     */
    public $accountingEmail;

    /**
     * @Assert\Length(max=13, min=13, allowEmptyString=true)
     * @Assert\Regex(pattern="/^\+[0-9]{0,12}$/")
     */
    public $accountingPhone;

    /**
     * @Assert\Length(max=250)
     */
    public $postalAddress;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        if (empty($id)) {
            throw new \InvalidArgumentException();
        }

        $this->id = $id;
    }
}
