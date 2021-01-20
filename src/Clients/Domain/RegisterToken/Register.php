<?php

namespace App\Clients\Domain\RegisterToken;

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\RegisterToken\ValueObject\RegisterId;
use App\Clients\Domain\RegisterToken\ValueObject\Token;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="company_register_request")
 */
class Register
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Clients\Domain\Client\Client", inversedBy="registerToken")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=false, unique=false)
     */
    private $client;

    /**
     * @ORM\Column(type="string", nullable=false, unique=false)
     */
    private $email;

    /**
     * @ORM\Embedded(class="App\Clients\Domain\RegisterToken\ValueObject\Token", columnPrefix=false)
     */
    private $token;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $additionalData;

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
        RegisterId $registerId,
        Client $client,
        Email $email,
        Token $token,
        \DateTimeInterface $dateTime
    ) {
        $this->id = $registerId;
        $this->client = $client;
        $this->email = $email;
        $this->token = $token;
        $this->createdAt = $dateTime;
        $this->updatedAt = $dateTime;
    }

    public static function create(
        RegisterId $registerId,
        Client $client,
        Email $email,
        Token $token,
        \DateTimeInterface $dateTime
    ): self {
        $self = new self($registerId, $client, $email, $token, $dateTime);

        return $self;
    }

    public static function createFromConsole(
        RegisterId $registerId,
        Client $client,
        Email $email,
        Token $token,
        \DateTimeInterface $dateTime,
        string $additionalData
    ): self {
        $self = new self($registerId, $client, $email, $token, $dateTime);
        $self->additionalData = $additionalData;

        return $self;
    }

    public function update(
        Email $email,
        Token $token
    ) {
        $this->email = $email;
        $this->token = $token;

        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}
