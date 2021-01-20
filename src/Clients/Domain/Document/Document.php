<?php

namespace App\Clients\Domain\Document;

use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Document\ValueObject\Amount;
use App\Clients\Domain\Document\ValueObject\DocumentNumber;
use App\Clients\Domain\Document\ValueObject\File;
use App\Clients\Domain\Document\ValueObject\Status;
use App\Clients\Domain\Document\ValueObject\Type;
use App\Clients\Domain\Invoice\Invoice;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="documents")
 */
class Document
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
     * @ORM\Column(name="number", type="string", nullable=true, unique=true)
     */
    private $number;

    /**
     * @ORM\Embedded(class="App\Clients\Domain\Document\ValueObject\File")
     */
    private $file;

    /**
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $amount;

    /**
     * @ORM\Column(name="type", type="smallint", nullable=false, options={"default": 0})
     */
    private $type;

    /**
     * @ORM\Column(name="status", type="smallint", nullable=false, options={"default": 0})
     */
    private $status;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    private function __construct(
        IdentityId $id,
        Client1CId $client1CId,
        File $file,
        Type $type,
        Status $status
    ) {
        $this->id = $id->getId();
        $this->client1CId = $client1CId->getValue();
        $this->file = $file;
        $this->type = $type->getValue();
        $this->status = $status->getValue();
        $this->number = null;
        $this->amount = 0;
    }

    public static function create(
        IdentityId $id,
        Client $client,
        File $file,
        Type $type,
        Status $status,
        \DateTimeImmutable $dateTime
    ): self {
        $clientId = new Client1CId($client->getClient1CId());

        $self = new self(
            $id,
            $clientId,
            $file,
            $type,
            $status
        );

        $self->createdAt = $dateTime;

        return $self;
    }

    public static function createWithClient1CId(
        IdentityId $id,
        string $client1cId,
        File $file,
        Type $type,
        Status $status,
        \DateTimeImmutable $dateTime
    ): self {
        $clientId = new Client1CId($client1cId);

        $self = new self(
            $id,
            $clientId,
            $file,
            $type,
            $status
        );

        $self->createdAt = $dateTime;

        return $self;
    }

    public static function createFromInvoice(Invoice $invoice, File $file, \DateTimeInterface $dateTime): self
    {
        $self = new self(
            IdentityId::next(),
            new Client1CId($invoice->getClient1CId()),
            $file,
            Type::invoice(),
            Status::formedByRequest()
        );

        $documentNumber = new DocumentNumber($invoice->getNumber());
        $self->number = $documentNumber->getValue();

        $amount = new Amount($invoice->getTotalWithValueTax());
        $self->amount = $amount->getValue();

        $self->createdAt = $dateTime;

        return $self;
    }

    public static function createUploadedDocument(
        Client1CId $client1CId,
        Type $type,
        File $file,
        \DateTimeInterface $dateTime
    ): self {
        $self = new self(
            IdentityId::next(),
            $client1CId,
            $file,
            $type,
            Status::formedAuto()
        );

        $self->createdAt = $dateTime;

        return $self;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return (string) $this->number;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @return float|int|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
