<?php

namespace App\Clients\Domain\Fuel\Type;

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelName;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelPurse;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\Fuel\Type\ValueObject\PurseCode;
use App\Clients\Domain\Fuel\Type\ValueObject\TypeId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="fuel_types",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"id", "fuel_code"})},
 *      indexes={@ORM\Index(columns={"fuel_code"})}
 * )
 */
class Type
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
     * @ORM\Column(name="fuel_name", type="string", nullable=false)
     */
    private $fuelName;

    /**
     * @ORM\Column(name="fuel_purse", type="boolean")
     */
    private $fuelPurse;

    /**
     * @ORM\Column(name="fuel_type", type="integer", nullable=true)
     */
    private $fuelType;

    /**
     * @ORM\Column(name="purse_code", type="integer", nullable=true)
     */
    private $purseCode;

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
        TypeId $id,
        FuelCode $fuelCode,
        FuelName $fuelName,
        FuelPurse $fuelPurse,
        FuelType $fuelType,
        PurseCode $purseCode
    ) {
        $this->id = $id;
        $this->fuelCode = $fuelCode->getValue();
        $this->fuelName = $fuelName->getValue();
        $this->fuelPurse = $fuelPurse->getValue();
        $this->fuelType = $fuelType->getValue();
        $this->purseCode = $purseCode->getValue();
    }

    public static function create(
        TypeId $id,
        FuelCode $fuelCode,
        FuelName $fuelName,
        FuelPurse $fuelPurse,
        FuelType $fuelType,
        PurseCode $purseCode,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self($id, $fuelCode, $fuelName, $fuelPurse, $fuelType, $purseCode);

        $self->createdAt = $createdAt;
        $self->updatedAt = $createdAt;

        return $self;
    }

    public function update(
        FuelName $fuelName,
        FuelPurse $fuelPurse,
        FuelType $fuelType,
        PurseCode $additionalType
    ): self {
        $this->fuelName = $fuelName;
        $this->fuelPurse = $fuelPurse;
        $this->fuelType = $fuelType;
        $this->purseCode = $additionalType;

        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getFuelName(): string
    {
        return $this->fuelName;
    }

    public function getFuelCode(): string
    {
        return $this->fuelCode;
    }
}
