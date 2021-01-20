<?php

namespace App\Clients\Domain\Transaction\Card\ValueObject;

use Webmozart\Assert\Assert;

final class Type
{
    private const WRITE_OFF = 0;
    private const RETURN = 1;
    private const REPLENISHMENT = 2;

    private static $names = [
        self::WRITE_OFF => 'write-off',
        self::RETURN => 'return',
        self::REPLENISHMENT => 'replenishment',
    ];

    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::oneOf($value, [
            self::WRITE_OFF,
            self::RETURN,
            self::REPLENISHMENT,
        ]);

        $this->value = $value;
    }

    public static function fromName(string $name): self
    {
        $names = array_flip(self::$names);

        return new self($names[$name]);
    }

    public static function getNames(): array
    {
        return self::$names;
    }

    public function getName(): string
    {
        return self::$names[$this->value];
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function isWriteOff(): bool
    {
        return self::WRITE_OFF === $this->getValue();
    }

    public function isReplenishment(): bool
    {
        return self::REPLENISHMENT === $this->getValue();
    }

    public function isReturn(): bool
    {
        return self::RETURN === $this->getValue();
    }

    public static function writeOff(): self
    {
        return new self(self::WRITE_OFF);
    }

    public static function return(): self
    {
        return new self(self::RETURN);
    }

    public static function replenishment(): self
    {
        return new self(self::REPLENISHMENT);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
