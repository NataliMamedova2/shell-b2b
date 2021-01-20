<?php

namespace App\Clients\Domain\CardOrder;

use App\Application\Domain\ValueObject\IdentityId;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cards_order")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Clients\Domain\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(name="count", type="integer", nullable=false)
     */
    private $count;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(name="phone", type="string", length=13, nullable=false)
     */
    private $phone;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    public function __construct(IdentityId $id, User $user, int $count, string $name, Phone $phone, \DateTimeInterface $dateTime)
    {
        $this->id = $id->getId();
        $this->user = $user;
        $this->count = $count;
        $this->name = $name;
        $this->phone = $phone->getValue();
        $this->createdAt = $dateTime;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}
