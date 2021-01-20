<?php

namespace App\Clients\Domain\User\Service;

interface PasswordEncoder
{
    public function encode(string $password): string;
}
