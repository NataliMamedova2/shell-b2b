<?php

namespace App\Api\Resource;

use App\Clients\Domain\Driver\Driver as DomainDriver;

final class DriverSearch implements Model
{
    public $id;
    public $name;

    /**
     * @param DomainDriver $driver
     *
     * @return Model
     */
    public function prepare($driver): Model
    {
        $name = $driver->getName();

        $this->id = $driver->getId();
        $this->name = $name->getFullName();

        return $this;
    }
}
