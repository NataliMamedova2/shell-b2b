<?php

namespace App\Clients\Domain\RegisterToken\Service;

interface TokenGenerator
{
    public function generate(): string;
}
