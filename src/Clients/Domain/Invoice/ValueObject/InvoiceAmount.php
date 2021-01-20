<?php

namespace App\Clients\Domain\Invoice\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
final class InvoiceAmount
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $valueTax;

    public function __construct(int $amount, ValueTax $valueTax)
    {
        Assert::greaterThanEq($amount, 0);
        Assert::lessThanEq($amount, 1000000);

        $this->amount = $amount * 100;
        $this->valueTax = $valueTax->getValue();
    }

    public function getValueTax(): int
    {
        return $this->valueTax;
    }
}
