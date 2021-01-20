<?php

namespace App\Clients\Domain\RefillBalance\ValueObject;

use Webmozart\Assert\Assert;

final class Amount
{
    /**
     * @var int
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::maxLength($value, 14);

        $this->value = \intval($value);
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
