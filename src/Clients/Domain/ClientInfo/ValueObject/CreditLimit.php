<?php

namespace App\Clients\Domain\ClientInfo\ValueObject;

use Webmozart\Assert\Assert;

final class CreditLimit
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
        return (int) $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
