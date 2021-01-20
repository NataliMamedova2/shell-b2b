<?php

declare(strict_types=1);

namespace App\Users\Domain\User\ValueObject;

use Webmozart\Assert\Assert;

final class Role
{
    private const ADMIN = 'ROLE_ADMIN';
    private const MANAGER = 'ROLE_MANAGER';
    private const MANAGER_CALL_CENTER = 'ROLE_MANAGER_CALL_CENTER';
    private const ACCOUNTANT = 'ROLE_ACCOUNTANT';
    private const PARTNER = 'ROLE_PARTNER';

    private static $names = [
        self::ADMIN => 'admin',
        self::MANAGER => 'manager',
        self::MANAGER_CALL_CENTER => 'manager-call-center',
        self::ACCOUNTANT => 'accountant',
        self::PARTNER => 'partner',
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
            self::MANAGER_CALL_CENTER,
            self::ACCOUNTANT,
            self::PARTNER,
        ]);

        $this->value = $value;
    }

    public static function formName(string $name): self
    {
        $values = array_flip(self::$names);

        return new self($values[$name]);
    }

    public static function getNames(): array
    {
        return self::$names;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getName(): string
    {
        $names = self::$names;

        return $names[$this->value];
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public static function getManagerCallCenterName()
    {
        return self::MANAGER_CALL_CENTER;
    }

    public static function getManagerName()
    {
        return self::MANAGER;
    }
}
