<?php

namespace App\Clients\Domain\Invoice\ValueObject;

final class InvoiceId
{
    /**
     * @var string
     */
    private $value;

    public function __construct(InvoiceNumber $invoiceNumber)
    {
        $this->value = date('Y').$invoiceNumber->getValue();
    }

    public function getId(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
