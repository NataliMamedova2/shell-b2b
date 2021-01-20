<?php

declare(strict_types=1);

namespace App\Clients\Domain\Driver\ValueObject;

use Webmozart\Assert\Assert;

final class Note
{
    private $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::maxLength($value, 250);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
