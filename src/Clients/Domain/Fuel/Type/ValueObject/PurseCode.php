<?php

declare(strict_types=1);

namespace App\Clients\Domain\Fuel\Type\ValueObject;

final class PurseCode
{
    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
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
