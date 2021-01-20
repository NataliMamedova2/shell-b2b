<?php

namespace App\Api\Resource;

use App\Api\Resource\Traits\PopulateObject;
use App\Clients\Domain\Transaction\Company\Transaction;

final class CompanyTransaction implements Model
{
    use PopulateObject;

    public $id;
    public $amount;
    public $type;
    public $createdAt;

    /**
     * @param Transaction $entity
     *
     * @return Model
     */
    public function prepare($entity): Model
    {
        $this->populateObject($entity);
        $this->createdAt = $entity->getDate();

        return $this;
    }
}
