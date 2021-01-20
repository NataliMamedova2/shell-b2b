<?php

namespace App\Api\Resource;

use App\Clients\Domain\Discount\Discount as DomainDiscount;

final class Discount implements Model
{
    public $id;
    public $sum;
    public $date;

    /**
     * @param DomainDiscount $discount
     *
     * @return Model
     */
    public function prepare($discount): Model
    {
        $this->id = $discount->getId();
        $this->sum = $discount->getDiscountSum();
        $this->date = $discount->getOperationDate();

        return $this;
    }
}
