<?php

namespace App\Clients\Domain\Transaction\Card;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="view_transactions_regions")
 */
class Region
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="code", type="string", unique=true, nullable=false)
     */
    private $code;

    /**
     * @ORM\Column(name="client_1c_id", type="string")
     */
    private $client1CId;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
