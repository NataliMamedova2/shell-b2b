<?php

namespace App\Clients\Domain\Driver\ValueObject;

use Webmozart\Assert\Assert;

final class CarNumber
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::maxLength($value, 12);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    public function __toString(): string
    {
        return (string) $this->getValue();
    }
}
