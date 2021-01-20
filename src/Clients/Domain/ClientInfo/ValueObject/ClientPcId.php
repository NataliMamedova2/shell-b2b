<?php

namespace App\Clients\Domain\ClientInfo\ValueObject;

use Webmozart\Assert\Assert;

final class ClientPcId
{
    /**
     * @var int
     */
    private $value;

    public function __construct(string $value)
    {
        $value = \intval($value);

        Assert::notEmpty($value);
        Assert::maxLength($value, 12);
        Assert::greaterThan($value, 0);

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
