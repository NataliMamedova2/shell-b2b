<?php

declare(strict_types=1);

namespace App\Clients\Domain\Client\ValueObject;

use Webmozart\Assert\Assert;

final class FixedSum
{
    /**
     * @var int
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::greaterThanEq($value, 0, 'The "FixedSum" must be a positive integer. Got: %s');
        Assert::maxLength($value, 14);
        $clearValue = preg_replace('/[^\d+]/', '', $value);

        $this->value = (int) $clearValue;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
