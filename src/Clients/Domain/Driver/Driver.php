<?php

namespace App\Clients\Domain\Driver;

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Driver\ValueObject\CarNumber;
use App\Clients\Domain\Driver\ValueObject\CarNumberId;
use App\Clients\Domain\Driver\ValueObject\DriverId;
use App\Clients\Domain\Driver\ValueObject\Name;
use App\Clients\Domain\Driver\ValueObject\Note;
use App\Clients\Domain\Driver\ValueObject\PhoneId;
use App\Clients\Domain\Driver\ValueObject\PhoneNumber;
use App\Clients\Domain\Driver\ValueObject\Status;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\Exception\DomainException;

/**
 * @ORM\Entity()
 * @ORM\Table(name="drivers")
 */
class Driver
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
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $email;

    /**
     * @ORM\Embedded(class="App\Clients\Domain\Driver\ValueObject\Name", columnPrefix=false)
     */
    private $name;

    /**
     * @var ArrayCollection|Phone[]
     *
     * @ORM\OneToMany(targetEntity="Phone", mappedBy="driver", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $phones;

    /**
     * @var ArrayCollection|\App\Clients\Domain\Driver\CarNumber[]
     *
     * @ORM\OneToMany(targetEntity="CarNumber", mappedBy="driver", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $carNumbers;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default": 0})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $note;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="updated_at", type="datetime_immutable", nullable=false)
     */
    private $updatedAt;

    private function __construct(DriverId $id, Client $client, Name $name)
    {
        $this->id = $id->getId();
        $this->client1CId = $client->getClient1CId();
        $this->name = $name;
        $this->phones = new ArrayCollection();
        $this->carNumbers = new ArrayCollection();
        $this->status = Status::active()->getValue();
    }

    public static function create(
        DriverId $id,
        Client $client,
        Name $name,
        Status $status,
        array $phones,
        \DateTimeInterface $dateTime,
        ?Email $email,
        ?Note $note
    ): self {
        $self = new self($id, $client, $name);

        $self->email = $email;
        $self->status = $status->getValue();
        $self->note = $note;

        $self->createdAt = $dateTime;
        $self->updatedAt = $dateTime;

        if (empty($phones)) {
            throw new DomainException('Must have min one phone number');
        }

        foreach ($phones as $phone) {
            $self->addPhoneNumber(new PhoneNumber($phone), $dateTime);
        }

        return $self;
    }

    public function addPhoneNumber(PhoneNumber $number, \DateTimeInterface $dateTime): void
    {
        foreach ($this->phones as $element) {
            if (true === $number->equals(new PhoneNumber($element->getNumber()))) {
                throw new DomainException(sprintf('Car number %s already added', $number->getValue()));
            }
        }

        $carNumber = new Phone(PhoneId::next(), $this, $number, $dateTime);
        $this->phones->add($carNumber);
    }

    public function update(
        Name $name,
        Status $status,
        array $phones,
        array $carNumbers,
        \DateTimeInterface $dateTime,
        ?Email $email,
        ?Note $note
    ): self {

        $this->name = $name;
        $this->email = $email;
        $this->status = $status->getValue();
        $this->note = $note;
        $this->updatedAt = $dateTime;

        if (empty($phones)) {
            throw new DomainException('Must have min one phone number');
        }

        $this->clearPhones();
        foreach ($phones as $phone) {
            $this->addPhoneNumber(new PhoneNumber($phone), $dateTime);
        }

        $this->clearCarNumbers();
        foreach ($carNumbers as $carNumber) {
            $this->addCarNumber(new CarNumber($carNumber), $dateTime);
        }

        return $this;
    }

    private function clearPhones(): void
    {
        $this->phones->clear();
    }

    private function clearCarNumbers(): void
    {
        $this->carNumbers->clear();
    }

    public function addCarNumber(CarNumber $number, \DateTimeInterface $dateTime): void
    {
        foreach ($this->carNumbers as $element) {
            if (true === $number->equals(new CarNumber($element->getNumber()))) {
                throw new DomainException(sprintf('Car number %s already added', $number->getValue()));
            }
        }

        $carNumber = new \App\Clients\Domain\Driver\CarNumber(CarNumberId::next(), $this, $number, $dateTime);
        $this->carNumbers->add($carNumber);
    }

    /**
     * @param Status $status
     */
    public function changeStatus(Status $status): void
    {
        $this->status = $status->getValue();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection|\App\Clients\Domain\Driver\CarNumber[]
     */
    public function getCarNumbers()
    {
        return $this->carNumbers;
    }

    /**
     * @return ArrayCollection|Phone[]
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}
