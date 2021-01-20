<?php

namespace App\Api\Resource;

use App\Clients\Domain\Fuel\Type\Type;

final class FuelTypeSearch implements Model
{
    public $id;
    public $name;

    /**
     * @param Type $fuelType
     *
     * @return Model
     */
    public function prepare($fuelType): Model
    {
        $this->id = $fuelType->getId();
        $this->name = $fuelType->getFuelName();

        return $this;
    }
}
