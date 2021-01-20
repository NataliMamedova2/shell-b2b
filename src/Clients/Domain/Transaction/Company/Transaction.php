<?php

namespace App\Clients\Domain\Transaction\Company;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="view_company_transactions")
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
     * @ORM\Column(name="client_1c_id", type="string")
     */
    private $client1CId;

    /**
     * @ORM\Column(name="fc_cbr_id", type="string")
     */
    private $fcCbrId;

    /**
     * @ORM\Column(name="amount", type="string", nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="date", type="datetime_immutable", nullable=false)
     */
    private $date;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
