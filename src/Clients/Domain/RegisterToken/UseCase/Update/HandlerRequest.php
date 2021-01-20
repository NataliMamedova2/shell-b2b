<?php

namespace App\Clients\Domain\RegisterToken\UseCase\Update;

use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;
use App\Application\Validator\Constraints as AppAssert;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=64)
     * @AppAssert\Email(mode="strict")
     */
    public $email;

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
