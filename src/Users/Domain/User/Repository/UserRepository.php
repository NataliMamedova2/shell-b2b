<?php

namespace App\Users\Domain\User\Repository;

use App\Users\Domain\User\User;
use Infrastructure\Interfaces\Repository\Repository;

interface UserRepository extends Repository
{
    public function findByUsernameOrEmail(string $username, string $email): ?User;
}
