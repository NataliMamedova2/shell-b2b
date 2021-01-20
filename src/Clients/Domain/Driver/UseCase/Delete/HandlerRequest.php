<?php

namespace App\Clients\Domain\Driver\UseCase\Delete;

use App\Clients\Domain\Driver\ValueObject\DriverId;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var DriverId
     */
    private $driverId;

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
