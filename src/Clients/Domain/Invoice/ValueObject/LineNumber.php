<?php

namespace App\Clients\Domain\Invoice\ValueObject;

use Webmozart\Assert\Assert;

final class LineNumber
{
    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, 0);

        $this->value = $value;
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
