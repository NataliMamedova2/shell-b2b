<?php

namespace App\Clients\Domain\Driver\UseCase\ChangeStatus;

use App\Clients\Domain\Driver\ValueObject\DriverId;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var DriverId
     */
    private $driverId;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"\App\Clients\Domain\Driver\ValueObject\Status", "getNames"})
     */
    public $status;

    public function __construct(DriverId $id)
    {
        $this->driverId = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->driverId->getId();
    }
}
