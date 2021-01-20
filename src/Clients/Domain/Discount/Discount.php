<?php


namespace App\Clients\Domain\Discount;

use App\Application\Domain\ValueObject\Client1CId;

use App\Clients\Domain\Discount\ValueObject\DiscountId;
use App\Clients\Domain\Discount\ValueObject\DiscountSum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="discounts")
 */
class Discount
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\Column(name="client_1c_id", type="string", nullable=false)
     */
    private $client1CId;

    /**
     * @ORM\Column(name="discount_sum", type="bigint", nullable=false)
     */
    private $discountSum;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="operation_date", type="datetime_immutable", nullable=false)
     */
    private $operationDate;

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
        DiscountId $id,
        Client1CId $client1CId,
        DiscountSum $discountSum,
        \DateTimeInterface $operationDate
    ) {
        $this->id = $id->getId();
        $this->client1CId = $client1CId->getValue();
        $this->discountSum = $discountSum->getValue();
        $this->operationDate = $operationDate;
    }

    public static function create(
        DiscountId $id,
        Client1CId $client1CId,
        DiscountSum $discountSum,
        \DateTimeInterface $operationDate,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self($id, $client1CId, $discountSum, $operationDate);

        $self->createdAt = $createdAt;
        $self->updatedAt = $createdAt;

        return $self;
    }

    public function update(
        DiscountSum $discountSum,
        \DateTimeInterface $operationDate
    ): self {
        $this->discountSum = $discountSum->getValue();
        $this->operationDate = $operationDate;

        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient1CId(): string
    {
        return $this->client1CId;
    }

    public function getDiscountSum(): int
    {
        return $this->discountSum;
    }

    public function getOperationDate(): \DateTimeInterface
    {
        return $this->operationDate;
    }
}
