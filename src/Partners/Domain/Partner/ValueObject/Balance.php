<?php

namespace App\Partners\Domain\Partner\ValueObject;

use Webmozart\Assert\Assert;

final class Balance
{
    /**
     * @var int
     */
    private $value;

    public function __construct($value)
    {
        Assert::numeric($value);
        Assert::maxLength($value, 14);

        $this->value = (int) ($value * 100);
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