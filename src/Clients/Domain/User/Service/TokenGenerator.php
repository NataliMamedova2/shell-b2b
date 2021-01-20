<?php

namespace App\Clients\Domain\User\Service;

interface TokenGenerator
{
    public function generate(): string;
}
