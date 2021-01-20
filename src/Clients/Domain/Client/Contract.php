<?php

namespace App\Clients\Domain\Client;

use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Client\ValueObject\ContractId;
use App\Clients\Domain\Client\ValueObject\DsgCaGhb;
use App\Clients\Domain\Client\ValueObject\EckDsgCa;
use App\Clients\Domain\Client\ValueObject\FixedSum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="clients_contracts",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"id", "client_1c_id"})},
 *      indexes={@ORM\Index(columns={"client_1c_id"})}
 * )
 */
class Contract
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
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $eckDsgCa;

    /**
     * @ORM\Column(type="bigint", length=14, nullable=false)
     */
    private $dsgCaGhb;

    /**
     * @ORM\Column(type="bigint", length=14, nullable=false)
     */
    private $fixedSum;

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
        ContractId $id,
        Client1CId $client1CId,
        EckDsgCa $eckDsgCa,
        DsgCaGhb $dsgCaGhb,
        FixedSum $fixedSum
    ) {
        $this->id = (string) $id;
        $this->client1CId = $client1CId->getValue();
        $this->eckDsgCa = $eckDsgCa->getValue();
        $this->dsgCaGhb = $dsgCaGhb->getValue();
        $this->fixedSum = $fixedSum->getValue();
    }

    public static function create(
        ContractId $id,
        Client1CId $client1CId,
        EckDsgCa $eckDsgCa,
        DsgCaGhb $dsgCaGhb,
        FixedSum $fixedSum,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self($id, $client1CId, $eckDsgCa, $dsgCaGhb, $fixedSum);

        $self->createdAt = $createdAt;
        $self->updatedAt = $createdAt;

        return $self;
    }

    public function update(
        EckDsgCa $eckDsgCa,
        DsgCaGhb $dsgCaGhb,
        FixedSum $fixedSum
    ): self {
        $this->eckDsgCa = $eckDsgCa->getValue();
        $this->dsgCaGhb = $dsgCaGhb->getValue();
        $this->fixedSum = $fixedSum->getValue();
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getClient1CId(): string
    {
        return $this->client1CId;
    }
}
