<?php

declare(strict_types=1);

namespace App\Users\Domain\User\ValueObject;

use Webmozart\Assert\Assert;

final class Status
{
    private const INACTIVE = 0;
    private const ACTIVE = 1;

    private static $names = [
        self::ACTIVE => 'active',
        self::INACTIVE => 'inactive',
    ];

    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::oneOf($value, [
            self::ACTIVE,
            self::INACTIVE,
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

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function inactive(): self
    {
        return new self(self::INACTIVE);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function getName(): string
    {
        $names = self::$names;

        return $names[$this->value];
    }

    public function isActive(): bool
    {
        return self::ACTIVE === $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
