<?php

namespace Tests\Unit\Clients\Domain\User\ValueObject;

use App\Clients\Domain\User\ValueObject\Role;
use PHPUnit\Framework\TestCase;

final class RoleTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new Role();
    }

    public function testCreateNotValidValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = 2;
        new Role($value);
    }

    /**
     * @param $value
     * @dataProvider validDataProvider
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new Role($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validDataProvider(): array
    {
        return [
            'admin' => ['ROLE_ADMIN'],
            'manager' => ['ROLE_MANAGER'],
            'accountant' => ['ROLE_ACCOUNTANT'],
        ];
    }

    /**
     * @param string $name
     * @param string $value
     * @dataProvider fromNameDataProvider
     */
    public function testCreateFromNameReturnObject(string $name, string $value): void
    {
        $result = Role::fromName($name);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function fromNameDataProvider(): array
    {
        return [
            ['admin', 'ROLE_ADMIN'],
            ['manager', 'ROLE_MANAGER'],
            ['accountant', 'ROLE_ACCOUNTANT'],
        ];
    }
}
