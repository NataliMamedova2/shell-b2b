<?php

namespace App\Clients\Domain\Company;

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\ValueObject\Accounting;
use App\Clients\Domain\Company\ValueObject\CompanyId;
use App\Clients\Domain\Company\ValueObject\Name;
use App\Clients\Domain\Company\ValueObject\PostalAddress;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="companies",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"client_id"})},
 *      indexes={@ORM\Index(columns={"client_id"})}
 * )
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Clients\Domain\Client\Client", inversedBy="company")
     */
    private $client;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(name="postal_address", type="string", length=255, nullable=true)
     */
    private $postalAddress;

    /**
     * @ORM\Embedded(class="App\Clients\Domain\Company\ValueObject\Accounting")
     */
    private $accounting;

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

    private function __construct(
        CompanyId $id,
        Client $client,
        Name $name,
        Email $email
    ) {
        $this->id = $id;
        $this->client = $client;
        $this->email = $email;
        $this->name = $name;
    }

    public static function register(
        CompanyId $id,
        Client $client,
        Email $email,
        \DateTimeInterface $dateTime
    ): self {
        $name = new Name($client->getFullName());
        $self = new self($id, $client, $name, $email);

        $self->createdAt = $dateTime;
        $self->updatedAt = $dateTime;

        return $self;
    }

    public function update(
        Name $name,
        Accounting $accounting,
        ?PostalAddress $postalAddress
    ) {
        $this->name = $name;
        $this->accounting = $accounting;
        $this->postalAddress = $postalAddress;
    }

    public function setEmail(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return (string) $this->id;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPostalAddress(): string
    {
        return (string) $this->postalAddress;
    }

    /**
     * @return Accounting
     */
    public function getAccounting(): Accounting
    {
        return $this->accounting;
    }
}
