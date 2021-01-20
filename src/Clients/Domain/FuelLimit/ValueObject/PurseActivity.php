<?php

namespace App\Clients\Domain\FuelLimit\ValueObject;

use Webmozart\Assert\Assert;

final class PurseActivity
{
    private const ACTIVE = 0;
    private const INACTIVE = 1;
    private const MARKED_FOR_REMOVAL = 2;

    /**
     * @var int
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::oneOf((int) $value, [
            self::ACTIVE,
            self::INACTIVE,
            self::MARKED_FOR_REMOVAL,
        ]);

        $this->value = (int) $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return (int) $this->value;
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function markToRemove(): self
    {
        return new self(self::MARKED_FOR_REMOVAL);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
