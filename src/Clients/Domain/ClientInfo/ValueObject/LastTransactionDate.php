<?php

namespace App\Clients\Domain\ClientInfo\ValueObject;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class LastTransactionDate
{
    /**
     * @ORM\Column(type="date_immutable", nullable=false)
     */
    private $date;

    /**
     * @ORM\Column(type="time_immutable", nullable=false)
     */
    private $time;

    public function __construct(\DateTimeImmutable $date, \DateTimeImmutable $time)
    {
        $this->date = $date;
        $this->time = $time;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getTime(): \DateTimeImmutable
    {
        return $this->time;
    }
}
