<?php

namespace App\Clients\Domain\Invoice\ValueObject;

use Webmozart\Assert\Assert;

final class InvoiceNumber
{
    private const PREFIX = 'СФ';
    private const SEPARATOR = '-';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $number)
    {
        Assert::notEmpty($number, 'The "InvoiceNumber" can\'t be empty. Got: %s');

        $this->value = self::PREFIX.self::SEPARATOR.$number;
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
