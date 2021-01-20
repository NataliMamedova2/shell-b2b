<?php

namespace App\Api\Resource;

use App\Clients\Infrastructure\ClientInfo\Service\Balance\Balance as BalanceObject;

final class Balance implements Model
{
    public $value;
    public $sign;

    /**
     * @param BalanceObject|null $balance
     *
     * @return Model
     */
    public function prepare($balance): ?Model
    {
        if (!$balance instanceof BalanceObject || empty($balance->getValue())) {
            return null;
        }

        $this->value = $balance->getAbsoluteValue();
        $this->sign = $balance->getSign();

        return $this;
    }
}
