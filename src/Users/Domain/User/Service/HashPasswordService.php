<?php

namespace App\Users\Domain\User\Service;

use App\Users\Domain\User\User;

interface HashPasswordService
{
    public function encode(User $user, string $plainPassword);
}
