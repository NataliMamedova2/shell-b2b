<?php

namespace App\Api\Resource;

use App\Clients\Domain\Driver\ValueObject\Status;

final class Driver implements Model
{
    public $id;
    public $firstName;
    public $lastName;
    public $middleName;
    public $email;
    public $phones = [];
    public $carsNumbers = [];
    public $status;
    public $note;

    /**
     * @param \App\Clients\Domain\Driver\Driver $driver
     *
     * @return Model
     */
    public function prepare($driver): Model
    {
        $name = $driver->getName();

        $this->id = $driver->getId();
        $this->firstName = $name->getFirstName();
        $this->lastName = $name->getLastName();
        $this->middleName = $name->getMiddleName();
        $this->email = $driver->getEmail();
        $this->note = $driver->getNote();
        $this->status = (new Status($driver->getStatus()))->getName();

        foreach ($driver->getPhones() as $phone) {
            $this->phones[] = ['number' => $phone->getNumber()];
        }

        foreach ($driver->getCarNumbers() as $carNumber) {
            $this->carsNumbers[] = ['number' => $carNumber->getNumber()];
        }

        return $this;
    }
}
