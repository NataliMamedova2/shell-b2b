<?php

declare(strict_types=1);

namespace App\Clients\Domain\User\ValueObject;

use Webmozart\Assert\Assert;

final class Role
{
    private const ADMIN = 'ROLE_ADMIN';
    private const MANAGER = 'ROLE_MANAGER';
    private const ACCOUNTANT = 'ROLE_ACCOUNTANT';

    private static $names = [
        self::ADMIN => 'admin',
        self::MANAGER => 'manager',
        self::ACCOUNTANT => 'accountant',
    ];

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::ADMIN,
            self::MANAGER,
            self::ACCOUNTANT,
        ]);

        $this->value = $value;
    }

    /**
     * @return array
     */
    public static function getNames(): array
    {
        return self::$names;
    }

    public static function fromName(string $name): self
    {
        $names = array_flip(self::$names);

        return new self($names[$name] ?? '');
    }

    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    public function getName(): string
    {
        return self::$names[$this->value];
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
