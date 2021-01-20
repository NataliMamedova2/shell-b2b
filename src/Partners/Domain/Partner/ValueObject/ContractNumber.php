<?php

declare(strict_types=1);

namespace App\Partners\Domain\Partner\ValueObject;

final class ContractNumber
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
        return (string) $this->value;
    }
}
