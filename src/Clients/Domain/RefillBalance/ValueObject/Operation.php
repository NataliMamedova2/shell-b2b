<?php

namespace App\Clients\Domain\RefillBalance\ValueObject;

use Webmozart\Assert\Assert;

final class Operation
{
    private const WRITE_OFF = 0;
    private const REFILL = 1;

    private static $names = [
        self::WRITE_OFF => 'write-off',
        self::REFILL => 'refill',
    ];

    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::oneOf($value, [
            self::WRITE_OFF,
            self::REFILL,
        ]);

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getSign(): string
    {
        if (self::WRITE_OFF === $this->value) {
            return '-';
        }

        return '+';
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
