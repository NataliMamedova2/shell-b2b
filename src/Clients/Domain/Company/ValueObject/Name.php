<?php

declare(strict_types=1);

namespace App\Clients\Domain\Company\ValueObject;

use Webmozart\Assert\Assert;

final class Name
{
    private $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::maxLength($value, 500);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return \strval($this->getValue());
    }
}
