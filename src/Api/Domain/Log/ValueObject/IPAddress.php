<?php

declare(strict_types=1);

namespace App\Api\Domain\Log\ValueObject;

use Domain\Exception\InvalidArgumentException;

final class IPAddress
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
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
