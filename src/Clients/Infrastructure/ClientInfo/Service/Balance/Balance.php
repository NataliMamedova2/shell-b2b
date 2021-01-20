<?php

namespace App\Clients\Infrastructure\ClientInfo\Service\Balance;

final class Balance
{
    /**
     * @var float
     */
    private $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return (int) $this->value;
    }

    public function getAbsoluteValue(): int
    {
        return (int) abs($this->getValue());
    }

    public function getSign(): string
    {
        return ($this->value < 0) ? '-' : '+';
    }
}
