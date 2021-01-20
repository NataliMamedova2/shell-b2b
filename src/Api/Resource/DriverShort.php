<?php

namespace App\Api\Resource;

use App\Clients\Domain\Driver\Driver as DomainDriver;

final class DriverShort implements Model
{
    public $id;
    public $firstName;
    public $lastName;
    public $middleName;
    public $carsNumbers = [];

    /**
     * @param DomainDriver $driver
     * @return Model
     */
    public function prepare($driver): Model
    {
        $name = $driver->getName();

        $this->id = $driver->getId();
        $this->firstName = $name->getFirstName();
        $this->lastName = $name->getLastName();
        $this->middleName = '';

        if (false === empty($name->getMiddleName())) {
            $this->middleName = $name->getMiddleName();
        }

        foreach ($driver->getCarNumbers() as $carNumber) {
            $this->carsNumbers[] = ['number' => $carNumber->getNumber()];
        }

        return $this;
    }
}
