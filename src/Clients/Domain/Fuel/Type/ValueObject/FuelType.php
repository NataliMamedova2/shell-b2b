<?php

declare(strict_types=1);

namespace App\Clients\Domain\Fuel\Type\ValueObject;

use Webmozart\Assert\Assert;

final class FuelType
{
    private const FUEL = 1;
    private const GOODS = 2;
    private const SERVICE = 3;

    private static $names = [
        self::FUEL => 'fuel',
        self::GOODS => 'goods',
        self::SERVICE => 'service',
    ];

    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::oneOf($value, [
            self::FUEL,
            self::GOODS,
            self::SERVICE,
        ]);

        $this->value = $value;
    }

    public static function fuel(): self
    {
        return new self(self::FUEL);
    }

    public static function goods(): self
    {
        return new self(self::GOODS);
    }

    public static function service(): self
    {
        return new self(self::SERVICE);
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

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
