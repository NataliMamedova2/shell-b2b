<?php

namespace App\Clients\Domain\Document\ValueObject;

use Webmozart\Assert\Assert;

final class DocumentNumber
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $number)
    {
        Assert::notEmpty($number);

        $this->value = $number;
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
