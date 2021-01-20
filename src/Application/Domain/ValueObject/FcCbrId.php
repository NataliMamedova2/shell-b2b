<?php

declare(strict_types=1);

namespace App\Application\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class FcCbrId
{
    /**
     * @var int
     */
    private $value;

    public function __construct(string $value)
    {
        $value = \intval($value);
        Assert::maxLength($value, 12);
        Assert::greaterThanEq($value, 0);

        $this->value = $value;
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
