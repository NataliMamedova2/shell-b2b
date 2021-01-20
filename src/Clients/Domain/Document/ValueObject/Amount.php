<?php

namespace App\Clients\Domain\Document\ValueObject;

use Webmozart\Assert\Assert;

final class Amount
{
    /**
     * @var float|int
     */
    private $value;

    public function __construct($value)
    {
        Assert::numeric($value);

        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
