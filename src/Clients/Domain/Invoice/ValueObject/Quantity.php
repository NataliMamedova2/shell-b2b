<?php

namespace App\Clients\Domain\Invoice\ValueObject;

use Webmozart\Assert\Assert;

final class Quantity
{
    private $value;

    public function __construct(float $value)
    {
        Assert::greaterThan($value, 0);
        Assert::lessThanEq($value, 50000);

        $this->value = $value * 100;
    }

    public function getValue(): float
    {
        return (float) $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
