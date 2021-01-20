<?php

namespace App\Clients\Domain\FuelLimit\ValueObject;

use Webmozart\Assert\Assert;

final class WeekLimit
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, sprintf('Value of "%s" is required', get_class($this)));
        Assert::maxLength($value, 14);

        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return (int) $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
