<?php

declare(strict_types=1);

namespace App\Application\Domain\ValueObject;

use App\Application\Domain\Exception\InvalidArgumentException;

final class Email
{
    private $value;

    public function __construct($value)
    {
        $filteredValue = filter_var($value, FILTER_VALIDATE_EMAIL);
        if (false === $filteredValue) {
            throw new InvalidArgumentException($value, ['string (valid email address)']);
        }
        $this->value = $filteredValue;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
