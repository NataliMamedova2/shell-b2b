<?php

declare(strict_types=1);

namespace App\Users\Domain\User\ValueObject;

final class Phone
{
    private $value;

    public function __construct(?string $value)
    {
        $this->value = $value;
    }

    public function equalTo($other): bool
    {
        return $other instanceof self && ((string) $this === (string) $other);
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue() ?? '';
    }
}
