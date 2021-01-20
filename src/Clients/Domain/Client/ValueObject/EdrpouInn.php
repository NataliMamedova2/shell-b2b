<?php

declare(strict_types=1);

namespace App\Clients\Domain\Client\ValueObject;

use Webmozart\Assert\Assert;

final class EdrpouInn
{
    /**
     * @var string
     */
    private $value;

    public function __construct(?string $value)
    {
        if ($value !== null) {
            Assert::maxLength($value, 12);
        }

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
