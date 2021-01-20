<?php

namespace App\Api\Action\Api\V1\FuelCard\ChangeDriverAction;

use App\Api\Resource\DriverShort;
use App\Api\Resource\Model;
use App\Clients\Domain\Card\Card;

final class DataTransformer implements \App\Api\Crud\Interfaces\DataTransformer
{
    /**
     * @return Model
     *
     * @var Card
     */
    public function transform($card)
    {
        $driver = $card->getDriver();

        $model = new DriverShort();

        return $model->prepare($driver);
    }
}
