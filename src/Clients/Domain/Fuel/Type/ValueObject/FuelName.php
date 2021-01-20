<?php

namespace App\Clients\Domain\Fuel\Type\ValueObject;

use Webmozart\Assert\Assert;

final class FuelName
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, sprintf('Value of "%s" is required', get_class($this)));
        Assert::maxLength($value, 60);

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
