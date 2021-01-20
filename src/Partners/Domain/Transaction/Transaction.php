<?php

namespace App\Partners\Domain\Transaction;

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcCode;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcName;
use App\Clients\Domain\Transaction\Card\ValueObject\Debit;
use App\Clients\Domain\Transaction\Card\ValueObject\FuelQuantity;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionCode;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionName;
use App\Clients\Domain\Transaction\Card\ValueObject\StellaPrice;
use App\Clients\Domain\Transaction\Card\ValueObject\Transaction1CId;
use App\Clients\Domain\Transaction\Card\ValueObject\TransactionId;
use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use App\Partners\Domain\Transaction\ValueObject\ClientPrice;
use App\Partners\Domain\Transaction\ValueObject\ClientSum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="partner_transactions",
 *      indexes={@ORM\Index(name="idx_partner_transactions_post_date", columns={"post_date"})}
 * )
 */
class Transaction
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_1c_id", type="string", unique=true, nullable=false)
     */
    private $transactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="client_1c_id", type="string", nullable=false)
     */
    private $client1CId;

    /**
     * @var string
     *
     * @ORM\Column(name="card_number", type="string", nullable=false)
     */
    private $cardNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="fuel_code", type="string", nullable=false)
     */
    private $fuelCode;

    /**
     * @var int
     *
     * @ORM\Column(name="fuel_quantity", type="bigint", nullable=false)
     */
    private $fuelQuantity;

    /**
     * @var int
     *
     * @ORM\Column(name="stella_price", type="bigint", nullable=false)
     */
    private $stellaPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="debit", type="bigint", nullable=false)
     */
    private $debit;

    /**
     * @var int
     *
     * @ORM\Column(name="client_price", type="bigint", nullable=false)
     */
    private $clientPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="client_sum", type="bigint", nullable=false)
     */
    private $clientSum;

    /**
     * @var string
     *
     * @ORM\Column(name="azs_code", type="string", nullable=false)
     */
    private $azsCode;

    /**
     * @var string
     *
     * @ORM\Column(name="azs_name", type="string", nullable=false)
     */
    private $azsName;

    /**
     * @var string
     *
     * @ORM\Column(name="region_code", type="string", nullable=false)
     */
    private $regionCode;

    /**
     * @var string
     *
     * @ORM\Column(name="region_name", type="string", nullable=false)
     */
    private $regionName;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="post_date", type="datetime_immutable", nullable=false)
     */
    private $postDate;

    /**
     * @var int
     *
     * @ORM\Column(name="write_off_type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $updatedAt;

    private function __construct(
        TransactionId $id,
        Transaction1CId $transaction1CId,
        Client1CId $client1CId,
        CardNumber $cardNumber,
        FuelCode $fuelCode,
        FuelQuantity $fuelQuantity,
        StellaPrice $stellaPrice,
        Debit $debit,
        ClientPrice $clientPrice,
        ClientSum $clientSum,
        AzcCode $azcCode,
        AzcName $azcName,
        RegionCode $regionCode,
        RegionName $regionName,
        \DateTimeInterface $postDate,
        Type $type
    ) {
        $this->id = $id;
        $this->transactionId = $transaction1CId;
        $this->client1CId = $client1CId->getValue();
        $this->cardNumber = $cardNumber->getValue();
        $this->fuelCode = $fuelCode->getValue();
        $this->fuelQuantity = $fuelQuantity->getValue();
        $this->stellaPrice = $stellaPrice->getValue();
        $this->debit = $debit->getValue();
        $this->clientPrice = $clientPrice->getValue();
        $this->clientSum = $clientSum->getValue();
        $this->azsCode = $azcCode->getValue();
        $this->azsName = $azcName->getValue();
        $this->regionCode = $regionCode->getValue();
        $this->regionName = $regionName->getValue();
        $this->postDate = $postDate;
        $this->type = $type->getValue();
    }

    public static function create(
        TransactionId $id,
        Transaction1CId $transaction1CId,
        Client1CId $client1CId,
        CardNumber $cardNumber,
        FuelCode $fuelCode,
        FuelQuantity $fuelQuantity,
        StellaPrice $stellaPrice,
        Debit $debit,
        ClientPrice $clientPrice,
        ClientSum $clientSum,
        AzcCode $azcCode,
        AzcName $azcName,
        RegionCode $regionCode,
        RegionName $regionName,
        \DateTimeInterface $postDate,
        Type $type,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self(
            $id,
            $transaction1CId,
            $client1CId,
            $cardNumber,
            $fuelCode,
            $fuelQuantity,
            $stellaPrice,
            $debit,
            $clientPrice,
            $clientSum,
            $azcCode,
            $azcName,
            $regionCode,
            $regionName,
            $postDate,
            $type
        );

        $self->createdAt = $createdAt;
        $self->updatedAt = $createdAt;

        return $self;
    }

    public function update(
        Client1CId $client1CId,
        CardNumber $cardNumber,
        FuelCode $fuelCode,
        FuelQuantity $fuelQuantity,
        StellaPrice $stellaPrice,
        Debit $debit,
        ClientPrice $clientPrice,
        ClientSum $clientSum,
        AzcCode $azcCode,
        AzcName $azcName,
        RegionCode $regionCode,
        RegionName $regionName,
        \DateTimeInterface $postDate,
        Type $type
    ): self {
        $this->client1CId = $client1CId->getValue();
        $this->cardNumber = $cardNumber->getValue();
        $this->fuelCode = $fuelCode->getValue();
        $this->fuelQuantity = $fuelQuantity->getValue();
        $this->stellaPrice = $stellaPrice->getValue();
        $this->debit = $debit->getValue();
        $this->clientPrice = $clientPrice->getValue();
        $this->clientSum = $clientSum->getValue();
        $this->azsCode = $azcCode->getValue();
        $this->azsName = $azcName->getValue();
        $this->regionCode = $regionCode->getValue();
        $this->regionName = $regionName->getValue();
        $this->postDate = $postDate;
        $this->type = $type->getValue();

        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient1CId(): string
    {
        return $this->client1CId;
    }

    public function getFuelCode(): string
    {
        return $this->fuelCode;
    }

    public function getTransaction1CId(): string
    {
        return $this->transactionId;
    }

    public function getFuelQuantity(): int
    {
        return (int) $this->fuelQuantity;
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    public function getAzsName(): string
    {
        return $this->azsName;
    }

    public function getDebit(): int
    {
        return (int) $this->debit;
    }

    public function getClientPrice(): int
    {
        return (int) $this->clientPrice;
    }

    public function getClientSum(): int
    {
        return (int) $this->clientSum;
    }

    public function getPrice(): int
    {
        return $this->stellaPrice;
    }

    public function getTypeName(): string
    {
        $type = new Type($this->type);

        return $type->getName();
    }

    public function isWriteOff(): bool
    {
        $type = new Type($this->type);

        return $type->isWriteOff();
    }

    public function isReturn(): bool
    {
        $type = new Type($this->type);

        return $type->isReturn();
    }

    public function isReplenishment(): bool
    {
        $type = new Type($this->type);

        return $type->isReplenishment();
    }

    public function getPostDate(): \DateTimeInterface
    {
        return $this->postDate;
    }
}
