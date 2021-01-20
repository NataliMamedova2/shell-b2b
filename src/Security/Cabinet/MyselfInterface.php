<?php

namespace App\Security\Cabinet;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\User\User;

interface MyselfInterface
{
    public function get(): User;

    public function getCompany(): Company;

    public function getClient(): Client;
}
