<?php

namespace App\Clients\Domain\ClientInfo;

use App\Application\Domain\ValueObject\IdentityId;
use Doctrine\ORM\Mapping as ORM;
use Domain\Exception\DomainException;

/**
 * @ORM\Entity()
 * @ORM\Table(name="clients_balance_history",
 *      indexes={@ORM\Index(columns={"client_pc_id"})}
 * )
 */
class BalanceHistory
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ClientInfo", inversedBy="balanceHistory")
     * @ORM\JoinColumn(name="client_pc_id", referencedColumnName="client_pc_id")
     */
    private $clientPc;

    /**
     * @ORM\Column(name="balance", type="bigint", nullable=false)
     */
    private $balance;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="date", type="date_immutable", nullable=false)
     */
    private $date;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    public function __construct(
        IdentityId $id,
        ClientInfo $clientInfo,
        \DateTimeInterface $dateTime
    ) {
        if ((int) $dateTime->format('j') !== 1) {
            throw new DomainException("It is not first day of month");
        }

        $this->id = $id->getId();
        $this->clientPc = $clientInfo;
        $this->balance = $clientInfo->getBalance();
        $this->date = $dateTime;
        $this->createdAt = $dateTime;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
