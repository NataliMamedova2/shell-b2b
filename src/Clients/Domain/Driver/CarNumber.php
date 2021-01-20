<?php

namespace App\Clients\Domain\Driver;

use Doctrine\ORM\Mapping as ORM;
use App\Clients\Domain\Driver\ValueObject\CarNumberId;

/**
 * @ORM\Entity()
 * @ORM\Table(name="drivers_cars_numbers")
 */
class CarNumber
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Clients\Domain\Driver\Driver", inversedBy="carNumbers")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $driver;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $number;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    public function __construct(CarNumberId $id, Driver $driver, ValueObject\CarNumber $number, \DateTimeInterface $dateTime)
    {
        $this->id = $id;
        $this->driver = $driver;
        $this->number = $number;
        $this->createdAt = $dateTime;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }
}
