<?php

declare(strict_types=1);

namespace App\Clients\Domain\Client\ValueObject;

use Webmozart\Assert\Assert;

final class NktId
{
    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::natural($value);
        Assert::maxLength($value, 12);

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
