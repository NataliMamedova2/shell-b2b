<?php

namespace App\Clients\Domain\Invoice\ValueObject;

use Webmozart\Assert\Assert;

final class ItemPrice
{
    private $value;

    public function __construct($value)
    {
        Assert::numeric($value);
        Assert::maxLength($value, 10);
        Assert::greaterThan($value, 0);

        $this->value = $value * 100;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
