<?php

namespace App\Partners\Domain\Transaction\ValueObject;

use Webmozart\Assert\Assert;

final class ClientPrice
{
    /**
     * @var int
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, sprintf('Value of "%s" is required', get_class($this)));
        Assert::maxLength($value, 18);
        Assert::greaterThan($value, 0);

        $this->value = (int) $value;
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
