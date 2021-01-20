<?php

namespace App\Api\Resource;

use App\Clients\Domain\Fuel\Type\Type;

final class Supply implements Model
{
    public $code;
    public $name;

    /**
     * @param Type $fuelType
     *
     * @return Model
     */
    public function prepare($fuelType): Model
    {
        $this->code = $fuelType->getFuelCode();
        $this->name = $fuelType->getFuelName();

        return $this;
    }
}
