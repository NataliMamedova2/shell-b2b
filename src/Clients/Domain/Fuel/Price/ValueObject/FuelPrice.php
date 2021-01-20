<?php

declare(strict_types=1);

namespace App\Clients\Domain\Fuel\Price\ValueObject;

use Webmozart\Assert\Assert;

final class FuelPrice
{
    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::notEmpty($value, sprintf('Value of "%s" is required', get_class($this)));
        Assert::maxLength($value, 10);
        Assert::greaterThan($value, 0);

        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
