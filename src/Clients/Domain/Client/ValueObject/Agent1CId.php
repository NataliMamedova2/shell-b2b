<?php

declare(strict_types=1);

namespace App\Clients\Domain\Client\ValueObject;

use Webmozart\Assert\Assert;

final class Agent1CId
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::maxLength($value, 10);

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
