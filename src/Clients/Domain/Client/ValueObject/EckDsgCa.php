<?php

declare(strict_types=1);

namespace App\Clients\Domain\Client\ValueObject;

use Webmozart\Assert\Assert;

final class EckDsgCa
{
    /**
     * @var int
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::greaterThanEq($value, 0, 'The "EckDsgCa" must be a positive integer. Got: %s');
        Assert::maxLength($value, 1);

        $this->value = (int) $value;
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
