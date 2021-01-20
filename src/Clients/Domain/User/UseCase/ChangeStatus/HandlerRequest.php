<?php

namespace App\Clients\Domain\User\UseCase\ChangeStatus;

use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Choice(callback={"\App\Clients\Domain\User\ValueObject\Status", "getNames"})
     */
    public $status;

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
