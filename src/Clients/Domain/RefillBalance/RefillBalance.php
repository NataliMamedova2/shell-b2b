<?php

namespace App\Clients\Domain\RefillBalance;

use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\RefillBalance\ValueObject\Amount;
use App\Clients\Domain\RefillBalance\ValueObject\CardOwner;
use App\Clients\Domain\RefillBalance\ValueObject\Operation;
use App\Clients\Domain\RefillBalance\ValueObject\OperationDate;
use App\Clients\Domain\RefillBalance\ValueObject\RefillBalanceId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="refill_balance")
 */
class RefillBalance
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $cardOwner;

    /**
     * @ORM\Column(name="fc_cbr_id", type="string")
     */
    private $fcCbrId;

    /**
     * @ORM\Column(type="integer")
     */
    private $operation;

    /**
     * @ORM\Column(type="bigint")
     */
    private $amount;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $operationDate;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    public function __construct(
        RefillBalanceId $id,
        CardOwner $cardOwner,
        FcCbrId $fcCbrId,
        Operation $operation,
        Amount $amount,
        OperationDate $operationDate
    ) {
        $this->id = $id;
        $this->cardOwner = $cardOwner->getValue();
        $this->fcCbrId = $fcCbrId->getValue();
        $this->operation = $operation->getValue();
        $this->amount = $amount->getValue();
        $this->operationDate = $operationDate->getValue();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function update(
        CardOwner $cardOwner,
        FcCbrId $fcCbrId,
        Operation $operation,
        Amount $amount,
        OperationDate $operationDate
    ): self {
        $this->cardOwner = $cardOwner->getValue();
        $this->fcCbrId = $fcCbrId->getValue();
        $this->operation = $operation->getValue();
        $this->amount = $amount->getValue();
        $this->operationDate = $operationDate->getValue();
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFcCbrId(): string
    {
        return $this->fcCbrId;
    }

    public function getOperationSign(): string
    {
        $operation = new Operation($this->operation);

        return $operation->getSign();
    }

    public function getAmount(): int
    {
        return (int) $this->amount;
    }

    public function getOperationDateTime(): \DateTimeImmutable
    {
        return $this->operationDate;
    }

    public function getCardOwner(): int
    {
        return $this->cardOwner;
    }

    public function getOperation(): int
    {
        return $this->operation;
    }
}
