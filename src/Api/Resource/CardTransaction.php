<?php

namespace App\Api\Resource;

use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Transaction\Card\Transaction;

final class CardTransaction implements Model
{
    public $id;
    public $cardNumber;
    public $fuelName;
    public $volume;
    public $networkStation;
    public $amount;
    public $price;
    public $status;
    public $createdAt;

    /**
     * @var Type[]
     */
    private $fuelTypes;

    public function __construct(array $fuelTypes)
    {
        $this->fuelTypes = $fuelTypes;
    }

    /**
     * @param Transaction $entity
     *
     * @return Model
     */
    public function prepare($entity): Model
    {
        $fuelName = '';
        $fuelType = $this->fuelTypes[$entity->getFuelCode()] ?? null;
        if ($fuelType instanceof Type) {
            $fuelName = $fuelType->getFuelName();
        }

        $this->id = $entity->getId();
        $this->cardNumber = $entity->getCardNumber();
        $this->fuelName = $fuelName;
        $this->volume = $entity->getFuelQuantity();
        $this->networkStation = $entity->getAzsName();
        $this->amount = $entity->getDebit();
        $this->price = $entity->getPrice();
        $this->status = $entity->getTypeName();
        $this->createdAt = $entity->getPostDate();

        return $this;
    }
}
