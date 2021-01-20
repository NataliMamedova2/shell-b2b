<?php

namespace App\Clients\Domain\Fuel\Price;

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Price\ValueObject\FuelPrice;
use App\Clients\Domain\Fuel\Price\ValueObject\PriceId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="fuel_prices",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"id", "fuel_code"})},
 *      indexes={@ORM\Index(columns={"fuel_code"})}
 * )
 */
class Price
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\Column(name="fuel_code", type="string", unique=true, nullable=false)
     */
    private $fuelCode;

    /**
     * @ORM\Column(name="fuel_price", type="integer", nullable=false)
     */
    private $fuelPrice;

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
        PriceId $id,
        FuelCode $fuelCode,
        FuelPrice $fuePrice
    ) {
        $this->id = $id;
        $this->fuelCode = $fuelCode->getValue();
        $this->fuelPrice = $fuePrice->getValue();
    }

    public static function create(
        PriceId $id,
        FuelCode $fuelCode,
        FuelPrice $fuePrice,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self($id, $fuelCode, $fuePrice);

        $self->createdAt = $createdAt;
        $self->updatedAt = $createdAt;

        return $self;
    }

    public function update(
        FuelPrice $fuePrice
    ): self {
        $this->fuelPrice = $fuePrice->getValue();

        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getFuelCode(): string
    {
        return $this->fuelCode;
    }

    public function getPriceWithTax(): int
    {
        return (int) $this->fuelPrice;
    }
}
