<?php

namespace App\Clients\Domain\Fuel\Type\UseCase\DeleteReplacementFuelType;

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
