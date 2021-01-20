<?php

namespace App\Clients\Infrastructure\User\Service;

use App\Clients\Domain\User\Service\TokenGenerator as DomainTokenGenerator;

final class TokenGenerator implements DomainTokenGenerator
{
    /**
     * @return string
     * @throws \Exception
     */
    public function generate(): string
    {
        return substr(bin2hex(random_bytes(32)), 0, 50);
    }
}
