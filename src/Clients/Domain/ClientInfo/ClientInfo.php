<?php

namespace App\Clients\Domain\ClientInfo;

use App\Application\Domain\ValueObject\FcCbrId;
use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\ClientInfo\ValueObject\Balance;
use App\Clients\Domain\ClientInfo\ValueObject\ClientPcId;
use App\Clients\Domain\ClientInfo\ValueObject\CreditLimit;
use App\Clients\Domain\ClientInfo\ValueObject\LastTransactionDate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="clients_info",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"id", "client_pc_id", "fc_cbr_id"})},
 *      indexes={@ORM\Index(columns={"fc_cbr_id"})}
 * )
 */
class ClientInfo
{
    /**
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\Id()
     * @ORM\Column(name="client_pc_id", type="bigint", length=12, nullable=false, unique=true)
     */
    private $clientPcId;

    /**
     * @ORM\Column(name="fc_cbr_id", type="string", length=10, nullable=false)
     */
    private $fcCbrId;

    /**
     * @ORM\Column(name="balance", type="bigint", nullable=false)
     */
    private $balance;

    /**
     * @ORM\Column(name="credit_limit", type="bigint", nullable=false)
     */
    private $creditLimit;

    /**
     * @ORM\Embedded(class="App\Clients\Domain\ClientInfo\ValueObject\LastTransactionDate")
     */
    private $lastTransaction;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="updated_at", type="datetime_immutable", nullable=false)
     */
    private $updatedAt;

    /**
     * @var ArrayCollection|BalanceHistory[]
     *
     * @ORM\OneToMany(targetEntity="BalanceHistory", mappedBy="clientPc", cascade={"persist"})
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $balanceHistory;

    private function __construct(
        IdentityId $id,
        ClientPcId $clientPcId,
        FcCbrId $fcCbrId,
        Balance $balance,
        CreditLimit $creditLimit,
        LastTransactionDate $transactionDate
    ) {
        $this->id = $id->getId();
        $this->clientPcId = $clientPcId->getValue();
        $this->fcCbrId = $fcCbrId->getValue();
        $this->balance = $balance->getValue();
        $this->creditLimit = $creditLimit->getValue();
        $this->lastTransaction = $transactionDate;

        $this->balanceHistory = new ArrayCollection();
    }

    public static function create(
        IdentityId $id,
        ClientPcId $clientPcId,
        FcCbrId $fcCbrId,
        Balance $balance,
        CreditLimit $creditLimit,
        LastTransactionDate $transactionDate,
        \DateTimeInterface $dateTime
    ): self {
        $self = new self($id, $clientPcId, $fcCbrId, $balance, $creditLimit, $transactionDate);

        $self->addHistory($dateTime);

        $self->createdAt = $dateTime;
        $self->updatedAt = $dateTime;

        return $self;
    }

    public function update(
        Balance $balance,
        CreditLimit $creditLimit,
        LastTransactionDate $transactionDate,
        \DateTimeInterface $dateTime
    ) {
        $this->balance = $balance->getValue();
        $this->creditLimit = $creditLimit->getValue();
        $this->lastTransaction = $transactionDate;
        $this->updatedAt = $dateTime;

        $this->addHistory($dateTime);
    }

    private function addHistory(\DateTimeInterface $date): void
    {
        if (1 !== (int) $date->format('j')) {
            return;
        }

        $existsInHistory = $this->balanceHistory->exists(function ($key, BalanceHistory $element) use ($date) {
            if ($element->getDate()->format('Y-m-d') === $date->format('Y-m-d')) {
                return true;
            }

            return false;
        });

        if (true === $existsInHistory) {
            return;
        }

        $history = new BalanceHistory(
            IdentityId::next(),
            $this,
            $date
        );

        $this->balanceHistory->add($history);
    }

    public function getClientPcId(): int
    {
        return $this->clientPcId;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getCreditLimit(): int
    {
        return $this->creditLimit;
    }
}
