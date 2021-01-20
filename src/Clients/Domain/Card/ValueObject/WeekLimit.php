<?php

namespace App\Clients\Domain\Card\ValueObject;

use Webmozart\Assert\Assert;

final class WeekLimit
{
    /**
     * @var int
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, sprintf('Value of "%s" is required', get_class($this)));
        Assert::maxLength($value, 15);

        $this->value = (int) $value;
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
