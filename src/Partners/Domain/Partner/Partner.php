<?php

namespace App\Partners\Domain\Partner;

use App\Application\Domain\ValueObject\Client1CId;
use App\Partners\Domain\Partner\ValueObject\Balance;
use App\Partners\Domain\Partner\ValueObject\ContractNumber;
use App\Partners\Domain\Partner\ValueObject\CreditLimit;
use App\Partners\Domain\Partner\ValueObject\Edrpou;
use App\Partners\Domain\Partner\ValueObject\EmitentNumber;
use App\Partners\Domain\Partner\ValueObject\Manager1CId;
use App\Partners\Domain\Partner\ValueObject\PartnerId;
use App\Partners\Domain\Partner\ValueObject\Title;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="partners",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"id", "client_1c_id"})},
 *      indexes={@ORM\Index(columns={"client_1c_id"})},
 *      indexes={@ORM\Index(columns={"manager_1c_id"})}
 * )
 */
class Partner
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\Column(name="client_1c_id", length=10, unique=true)
     */
    private $client1CId;

    /**
     * @ORM\Column(name="title", type="string", length=25, nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(name="edrpou", type="string", length=12)
     */
    private $edrpou;

    /**
     * @ORM\Column(type="string", nullable=false, name="emitent_number")
     */
    private $emitentNumber;

    /**
     * @ORM\Column(name="manager_1c_id", type="string", length=12, nullable=false)
     */
    private $manager1CId;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $contractNumber;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $contractDate;

    /**
     * @ORM\Column(name="balance", type="bigint", nullable=false)
     */
    private $balance;

    /**
     * @ORM\Column(name="sota_token", type="string", length=33)
     */
    private $sotaToken;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="updated_at", type="datetime_immutable", nullable=false)
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="credit_limit", type="bigint", nullable=false)
     */
    private $creditLimit;

    private function __construct(
        PartnerId $id,
        Client1CId $client1CId,
        Title $title,
        Edrpou $edrpou,
        Manager1CId $manager1CId,
        EmitentNumber $emitentNumber,
        Balance $balance,
        CreditLimit $creditLimit
    ) {
        $this->id = $id;
        $this->client1CId = $client1CId;
        $this->title = $title->getValue();
        $this->edrpou = $edrpou;
        $this->manager1CId = $manager1CId;
        $this->emitentNumber = $emitentNumber;
        $this->balance = $balance->getValue();
        $this->creditLimit = $creditLimit->getValue();
    }

    public static function create(
        PartnerId $id,
        Client1CId $client1CId,
        Title $title,
        Edrpou $edrpou,
        Manager1CId $manager1CId,
        EmitentNumber $emitentNumber,
        Balance $balance,
        CreditLimit $creditLimit,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self($id, $client1CId, $title, $edrpou, $manager1CId, $emitentNumber, $balance, $creditLimit);

        $self->createdAt = $createdAt;
        $self->updatedAt = $createdAt;

        return $self;
    }

    public static function createWithContract(
        PartnerId $id,
        Client1CId $client1CId,
        Title $title,
        Edrpou $edrpou,
        Manager1CId $manager1CId,
        EmitentNumber $emitentNumber,
        Balance $balance,
        ContractNumber $contractNumber,
        CreditLimit $creditLimit,
        \DateTimeInterface $contractDate,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self($id, $client1CId, $title, $edrpou, $manager1CId, $emitentNumber, $balance, $creditLimit);

        $self->createdAt = $createdAt;
        $self->updatedAt = $createdAt;
        $self->contractNumber = $contractNumber;
        $self->contractDate = $contractDate;

        return $self;
    }

    public function update(
        Title $title,
        Client1CId $client1CId,
        Edrpou $edrpou = null,
        Manager1CId $manager1CId = null
    ): self {
        $this->title = $title;
        $this->edrpou = $edrpou;
        $this->manager1CId = $manager1CId;
        $this->client1CId = $client1CId;

        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function updateWithContract(
        Title $title,
        Client1CId $client1CId,
        ContractNumber $contractNumber,
        \DateTimeInterface $contractDate,
        Edrpou $edrpou = null,
        Manager1CId $manager1CId = null
    ): self {
        $this->title = $title;
        $this->edrpou = $edrpou;
        $this->contractNumber = $contractNumber;
        $this->contractDate = $contractDate;
        $this->manager1CId = $manager1CId;
        $this->client1CId = $client1CId;

        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getClient1CId(): string
    {
        return $this->client1CId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(Title $title)
    {
        $this->title = $title;
    }

    public function getEdrpou(): ?string
    {
        return $this->edrpou;
    }

    public function setEdrpouInn(Edrpou $edrpou)
    {
        $this->edrpou = $edrpou;
    }

    public function getManager1CId(): string
    {
        return $this->manager1CId;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getEmitentNumber(): int
    {
        return (int) $this->emitentNumber;
    }

    public function getContractNumber(): string
    {
        return (string) $this->contractNumber;
    }

    public function getContractDate(): ?\DateTimeInterface
    {
        return $this->contractDate;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getSotaToken(): ?string
    {
        return $this->sotaToken;
    }

    public function setSotaToken(string $sotaToken)
    {
        $this->sotaToken = $sotaToken;
    }

    public function getCreditLimit()
    {
        return $this->creditLimit;
    }
}
