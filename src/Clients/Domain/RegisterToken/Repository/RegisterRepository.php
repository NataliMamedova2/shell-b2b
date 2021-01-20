<?php

namespace App\Clients\Domain\RegisterToken\Repository;

use App\Clients\Domain\RegisterToken\Register;
use Infrastructure\Interfaces\Repository\Repository;

interface RegisterRepository extends Repository
{
    public function findByToken(string $token): ?Register;
}
