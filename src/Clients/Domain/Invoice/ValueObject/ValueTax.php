<?php

namespace App\Clients\Domain\Invoice\ValueObject;

use Webmozart\Assert\Assert;

final class ValueTax
{
    /**
     * @var int
     */
    private $value;

    public function __construct(int $taxValue)
    {
        Assert::numeric($taxValue);
        Assert::greaterThan($taxValue, 1);

        $this->value = $taxValue * 100;
    }

    public function getValue(): int
    {
        return (int) $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
