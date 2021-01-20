<?php

namespace App\Clients\Domain\User\Repository;

use App\Clients\Domain\User\User;
use Infrastructure\Interfaces\Repository\Repository;

interface UserRepository extends Repository
{
    public function findByUsernameOrEmail(string $username, string $email): ?User;

    public function findByToken(string $token): ?User;
}
