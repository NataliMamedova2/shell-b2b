<?php

namespace App\Clients\Domain\Card\ValueObject;

use Webmozart\Assert\Assert;

final class CardStatus
{
    private const BLOCKED = 0;
    private const ACTIVE = 1;

    private static $names = [
        self::BLOCKED => 'blocked',
        self::ACTIVE => 'active',
    ];

    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::oneOf($value, [
            self::ACTIVE,
            self::BLOCKED,
        ]);

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getName(): string
    {
        return self::$names[$this->value];
    }

    public static function fromName(string $name): self
    {
        $names = array_flip(self::$names);

        return new self($names[$name]);
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

    public static function blocked(): self
    {
        return new self(self::BLOCKED);
    }

    public function isBlocked(): bool
    {
        return self::BLOCKED === $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
