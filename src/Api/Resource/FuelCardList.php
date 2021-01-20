<?php

namespace App\Api\Resource;

use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\ValueObject\CardStatus;

final class FuelCardList implements Model
{
    use Traits\PopulateObject;

    public $id;
    public $cardNumber;
    public $onModeration = false;
    public $status;
    public $driver = null;
    private $limitsCardNumbersOnModeration = [];

    public function __construct(array $limitsCardNumbersOnModeration)
    {
        $this->limitsCardNumbersOnModeration = $limitsCardNumbersOnModeration;
    }

    /**
     * @param Card $fuelCard
     *
     * @return Model
     */
    public function prepare($fuelCard): Model
    {
        $this->populateObject($fuelCard);

        $this->status = (new CardStatus($fuelCard->getStatus()))->getName();
        if (true === $fuelCard->getExportStatus()->onModeration() || true === in_array($fuelCard->getCardNumber(), $this->limitsCardNumbersOnModeration)) {
            $this->onModeration = true;
        }

        $driver = $fuelCard->getDriver();
        if ($driver instanceof \App\Clients\Domain\Driver\Driver) {
            $driverResource = new DriverShort();
            $this->driver = $driverResource->prepare($driver);
        }

        return $this;
    }
}
