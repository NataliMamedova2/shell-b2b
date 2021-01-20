<?php

namespace App\Clients\Domain\Company\ValueObject;

use Webmozart\Assert\Assert;

final class PostalAddress
{
    private $value;

    public function __construct(string $value)
    {
        Assert::maxLength($value, 250);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return (string) $this->value;
    }

    public function __toString(): string
    {
        return \strval($this->getValue());
    }
}
