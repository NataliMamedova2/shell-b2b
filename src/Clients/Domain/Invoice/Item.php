<?php

namespace App\Clients\Domain\Invoice;

use App\Application\Domain\ValueObject\FuelCode;
use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\Invoice\ValueObject\ItemPrice;
use App\Clients\Domain\Invoice\ValueObject\LineNumber;
use App\Clients\Domain\Invoice\ValueObject\Quantity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Table part of Invoice.
 *
 * @ORM\Entity()
 * @ORM\Table(name="invoices_items")
 */
class Item
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Clients\Domain\Invoice\Invoice", inversedBy="items")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $invoice;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default": 0})
     */
    private $lineNumber;

    /**
     * @ORM\Column(name="fuel_code", type="string", nullable=false)
     */
    private $fuelCode;

    /**
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private $quantity;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    public function __construct(
        IdentityId $id,
        Invoice $invoice,
        LineNumber $lineNumber,
        FuelCode $fuelCode,
        ItemPrice $price,
        Quantity $quantity,
        \DateTimeInterface $dateTime
    ) {
        $this->id = $id->getId();
        $this->invoice = $invoice;
        $this->lineNumber = $lineNumber->getValue();
        $this->fuelCode = $fuelCode->getValue();
        $this->price = $price->getValue();
        $this->quantity = $quantity->getValue();

        $this->createdAt = $dateTime;
    }

    /**
     * @return Invoice
     */
    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    public function getFuelCode(): string
    {
        return $this->fuelCode;
    }

    /**
     * @return float|int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return float|int
     */
    public function getPriceWithValueTax()
    {
        return $this->price;
    }

    /**
     * @return float|int
     */
    public function getPriceWithoutValueTax()
    {
        $valueTax = $this->invoice->getValueTax() / 100;
        $multiplier = 10000000;

        return ceil((($this->price / 100) / (100 + $valueTax) * 100) * $multiplier);
    }

    /**
     * @return float|int
     */
    public function getSumWithValueTax()
    {
        $multiplier = 100;

        return (($this->quantity / 100) * ($this->price / 100)) * $multiplier;
    }

    /**
     * @return float|int
     */
    public function getSumWithoutValueTax()
    {
        $valueTax = $this->invoice->getValueTax() / 100;
        $multiplier = 100;

        return (($this->quantity / 100) * ($this->price / 100) / (100 + $valueTax) * 100) * $multiplier;
    }

    /**
     * @return float|int
     */
    public function getSumValueTax()
    {
        $valueTax = $this->invoice->getValueTax() / 100;
        $multiplier = 100;

        return (($this->quantity / 100) * ($this->price / 100) / (100 + $valueTax) * $valueTax) * $multiplier;
    }
}
