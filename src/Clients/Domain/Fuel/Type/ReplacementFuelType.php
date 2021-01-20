<?php

namespace App\Clients\Domain\Fuel\Type;

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelName;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelPurse;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\Fuel\Type\ValueObject\PurseCode;
use App\Clients\Domain\Fuel\Type\ValueObject\RepacementTypeId;
use App\Clients\Domain\Fuel\Type\ValueObject\TypeId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="replacement_fuel_types",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"id", "fuel_code"})},
 *      indexes={@ORM\Index(columns={"fuel_code"})}
 * )
 */
class ReplacementFuelType
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
     * @ORM\Column(name="fuel_code", type="string", unique=true, nullable=false)
     */
    private $fuelCode;

    /**
     * @ORM\Column(name="fuel_replacement_code", type="string", unique=false, nullable=false)
     */
    private $fuelReplacementCode;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    private function __construct(
        RepacementTypeId $id,
        FuelCode $fuelCode,
        FuelCode $fuelReplacementCode
    ) {
        $this->id = $id;
        $this->fuelCode = $fuelCode->getValue();
        $this->fuelReplacementCode = $fuelReplacementCode->getValue();
    }

    public static function create(
        RepacementTypeId $id,
        FuelCode $fuelCode,
        FuelCode $fuelReplacementCode
    ): self {
        $self = new self($id, $fuelCode, $fuelReplacementCode);

        $self->createdAt =  new \DateTimeImmutable();

        return $self;
    }

    public function update(
        FuelCode $fuelCode,
        FuelCode $fuelReplacementCode
    ): self {
        $this->fuelCode = $fuelCode;
        $this->fuelReplacementCode = $fuelReplacementCode;

        return $this;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getFuelCode(): string
    {
        return $this->fuelCode;
    }

    public function getFuelReplacementCode(): string
    {
        return $this->fuelReplacementCode;
    }
}
