<?php

namespace App\Clients\Domain\Fuel\Type\ValueObject;

final class FuelPurse
{
    /**
     * @var bool
     */
    private $value;

    public function __construct($value)
    {
        $this->value = (bool) $value;
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
