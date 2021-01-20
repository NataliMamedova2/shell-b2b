<?php

namespace App\Clients\Domain\Card\ValueObject;

use Webmozart\Assert\Assert;

final class CarNumber
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::maxLength($value, 15);

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
