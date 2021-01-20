<?php

namespace Tests\Unit\Clients\Infrastructure\RegisterToken\Service;

use App\Clients\Infrastructure\RegisterToken\Service\TokenGenerator;
use PHPUnit\Framework\TestCase;

final class TokenGeneratorTest extends TestCase
{
    public function testGenerateReturnString(): void
    {
        $generator = new TokenGenerator();

        $token = $generator->generate();

        $this->assertIsString($token);
    }
}
