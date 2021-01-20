<?php

namespace Tests\Unit\Clients\Domain\User\ValueObject;

use App\Clients\Domain\User\ValueObject\Token;
use PHPUnit\Framework\TestCase;

final class TokenTest extends TestCase
{
    public function testCreateEmptyTokenReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Token('');
    }

    public function testCreateValidDataTokenNotExpire(): void
    {
        $token = 'securetoken';
        $result = new Token($token);

        $this->assertEquals($token, $result->getToken());
        $this->assertEquals(false, $result->isExpiredTo(new \DateTimeImmutable()));
    }

    public function testCreateValidDataTokenExpire(): void
    {
        $token = 'securetoken';
        $result = new Token($token);

        $this->assertEquals($token, $result->getToken());
        $this->assertEquals(true, $result->isExpiredTo(new \DateTimeImmutable('+7 days 1 hour')));
    }
}
