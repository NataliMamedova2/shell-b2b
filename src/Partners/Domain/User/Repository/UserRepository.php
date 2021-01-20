<?php

namespace App\Partners\Domain\User\Repository;

use App\Partners\Domain\User\User;
use Infrastructure\Interfaces\Repository\Repository;

interface UserRepository extends Repository
{
    public function findByUsernameOrEmail(string $username, string $email): ?User;
}
