<?php

namespace App\Api\Resource;

use App\Api\Resource\Traits\PopulateObject;
use App\Clients\Domain\Transaction\Card\Region;

final class TransactionRegion implements Model
{
    use PopulateObject;

    public $code;
    public $name;

    /**
     * @param Region $region
     *
     * @return Model
     */
    public function prepare($region): Model
    {
        $this->populateObject($region);

        return $this;
    }
}
