<?php

namespace App\Clients\Infrastructure\ClientInfo\Service\Balance;

interface BalanceService
{
    public function getBalance(): Balance;
}
