<?php

namespace App\Clients\Domain\Invoice;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Domain\ValueObject\FuelCode;
use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Invoice\ValueObject\Date;
use App\Clients\Domain\Invoice\ValueObject\InvoiceId;
use App\Clients\Domain\Invoice\ValueObject\InvoiceNumber;
use App\Clients\Domain\Invoice\ValueObject\ItemPrice;
use App\Clients\Domain\Invoice\ValueObject\LineNumber;
use App\Clients\Domain\Invoice\ValueObject\Quantity;
use App\Clients\Domain\Invoice\ValueObject\ValueTax;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\Exception\DomainException;

/**
 * Main part of Invoice.
 *
 * @ORM\Entity()
 * @ORM\Table(name="invoices")
 */
class Invoice
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\Column(name="client_1c_id", type="string", nullable=false)
     */
    private $client1CId;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $invoiceId;

    /**
     * @ORM\Column(name="number", type="string", nullable=false, unique=true)
     */
    private $number;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $valueTax;

    /**
     * @ORM\Embedded(class="App\Clients\Domain\Invoice\ValueObject\Date", columnPrefix=false)
     */
    private $date;

    /**
     * @ORM\Embedded(class="App\Application\Domain\ValueObject\ExportStatus", columnPrefix=false)
     */
    private $exportStatus;

    /**
     * @var ArrayCollection|Item[]
     *
     * @ORM\OneToMany(targetEntity="App\Clients\Domain\Invoice\Item", mappedBy="invoice", cascade={"persist"})
     * @ORM\OrderBy({"lineNumber" = "ASC"})
     */
    private $items;

    private function __construct(
        IdentityId $id,
        string $client1CId,
        InvoiceNumber $number,
        ValueTax $valueTax,
        Date $date
    ) {
        $this->id = $id->getId();
        $this->client1CId = $client1CId;
        $this->invoiceId = (new InvoiceId($number))->getId();
        $this->number = $number->getValue();
        $this->valueTax = $valueTax->getValue();
        $this->date = $date;
        $this->exportStatus = ExportStatus::new();

        $this->items = new ArrayCollection();
    }

    public static function create(
        IdentityId $id,
        string $client1CId,
        InvoiceNumber $number,
        ValueTax $valueTax,
        Date $date
    ): self {
        $self = new self($id, $client1CId, $number, $valueTax, $date);

        $self->exportStatus->readyForExport();

        return $self;
    }

    public function addItem(
        LineNumber $lineNumber,
        FuelCode $fuelCode,
        ItemPrice $price,
        Quantity $quantity,
        \DateTimeInterface $dateTime
    ) {
        $item = new Item(IdentityId::next(), $this, $lineNumber, $fuelCode, $price, $quantity, $dateTime);

        foreach ($this->items as $element) {
            if ($element->getFuelCode() === $fuelCode->getValue()) {
                throw new DomainException('Item already added');
            }
        }

        $this->items->add($item);
    }

    /**
     * @return Item[]|ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient1CId(): string
    {
        return $this->client1CId;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getInvoiceId(): string
    {
        return $this->invoiceId;
    }

    public function getValueTax(): int
    {
        return $this->valueTax;
    }

    public function getDate(): Date
    {
        return $this->date;
    }

    /**
     * @return float|int
     */
    public function getTotalWithoutValueTax()
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item->getSumWithoutValueTax();
        }

        return $sum;
    }

    /**
     * @return float|int
     */
    public function getTotalValueTax()
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item->getSumValueTax();
        }

        return $sum;
    }

    /**
     * @return float|int
     */
    public function getTotalWithValueTax()
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item->getSumWithValueTax();
        }

        return $sum;
    }

    public function getExportStatus(): ExportStatus
    {
        return $this->exportStatus;
    }
}
