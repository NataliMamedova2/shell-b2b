<?php

namespace App\Clients\Domain\Client;

use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\Client\ValueObject\Agent1CId;
use App\Clients\Domain\Client\ValueObject\ClientId;
use App\Clients\Domain\Client\ValueObject\ContractNumber;
use App\Clients\Domain\Client\ValueObject\EdrpouInn;
use App\Clients\Domain\Client\ValueObject\FullName;
use App\Clients\Domain\Client\ValueObject\Manager1CId;
use App\Clients\Domain\Client\ValueObject\NktId;
use App\Clients\Domain\Client\ValueObject\Status;
use App\Clients\Domain\Client\ValueObject\Type;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\RegisterToken\Register;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="clients",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"id", "client_1c_id"})},
 *      indexes={@ORM\Index(columns={"client_1c_id"})}
 * )
 */
class Client
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\Column(name="client_1c_id", length=10, unique=true)
     */
    private $client1CId;

    /**
     * @var Company
     *
     * @ORM\OneToOne(targetEntity="App\Clients\Domain\Company\Company", mappedBy="client")
     */
    private $company;

    /**
     * @var PersistentCollection|Register[]
     *
     * @ORM\OneToMany(targetEntity="App\Clients\Domain\RegisterToken\Register", mappedBy="client")
     */
    private $registerToken;

    /**
     * @ORM\Column(name="full_name", type="string", length=500, nullable=false)
     */
    private $fullName;

    /**
     * @ORM\Column(name="edrpou_inn", type="string", length=12)
     */
    private $edrpouInn;

    /**
     * @ORM\Column(name="sota_token", type="string", length=33)
     */
    private $sotaToken;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default": 0})
     */
    private $type;

    /**
     * @ORM\Column(name="nkt_id", type="bigint", length=12, nullable=false)
     */
    private $nktId;

    /**
     * @ORM\Column(name="manager_1c_id", type="string", length=12, nullable=false)
     */
    private $manager1CId;

    /**
     * @ORM\Column(name="agent_1c_id", type="string", length=10, nullable=false)
     */
    private $agent1CId;

    /**
     * @ORM\Column(name="fc_cbr_id", type="string", length=10, nullable=false)
     */
    private $fcCbrId;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default": 0})
     */
    private $status;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $contractNumber;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $contractDate;

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
        ClientId $id,
        Client1CId $client1CId,
        FullName $fullName,
        EdrpouInn $edrpouInn,
        Type $type,
        NktId $nktId,
        Manager1CId $manager1CId,
        Agent1CId $agent1CId,
        FcCbrId $fcCbrId,
        Status $status
    ) {
        $this->id = $id;
        $this->client1CId = $client1CId;
        $this->fullName = $fullName;
        $this->edrpouInn = $edrpouInn;
        $this->type = $type->getValue();
        $this->nktId = $nktId->getValue();
        $this->manager1CId = $manager1CId;
        $this->agent1CId = $agent1CId;
        $this->fcCbrId = $fcCbrId;
        $this->status = $status->getValue();
    }

    public static function create(
        ClientId $id,
        Client1CId $client1CId,
        FullName $fullName,
        EdrpouInn $edrpouInn,
        Type $type,
        NktId $nktId,
        Manager1CId $manager1CId,
        Agent1CId $agent1CId,
        FcCbrId $fcCbrId,
        Status $status,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self($id, $client1CId, $fullName, $edrpouInn, $type, $nktId, $manager1CId, $agent1CId, $fcCbrId, $status);

        $self->createdAt = $createdAt;
        $self->updatedAt = $createdAt;

        return $self;
    }

    public static function createWithContract(
        ClientId $id,
        Client1CId $client1CId,
        FullName $fullName,
        EdrpouInn $edrpouInn,
        Type $type,
        NktId $nktId,
        Manager1CId $manager1CId,
        Agent1CId $agent1CId,
        FcCbrId $fcCbrId,
        Status $status,
        ContractNumber $contractNumber,
        \DateTimeInterface $contractDate,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self($id, $client1CId, $fullName, $edrpouInn, $type, $nktId, $manager1CId, $agent1CId, $fcCbrId, $status);

        $self->contractNumber = $contractNumber;
        $self->contractDate = $contractDate;
        $self->createdAt = $createdAt;
        $self->updatedAt = $createdAt;

        return $self;
    }

    public function updateWithContract(
        FullName $fullName,
        EdrpouInn $edrpouInn,
        Type $type,
        NktId $nktId,
        Manager1CId $manager1CId,
        Agent1CId $agent1CId,
        FcCbrId $fcCbrId,
        Status $status,
        ContractNumber $contractNumber,
        \DateTimeInterface $contractDate
    ): self {
        $this->fullName = $fullName;
        $this->edrpouInn = $edrpouInn;
        $this->type = $type->getValue();
        $this->nktId = $nktId->getValue();
        $this->manager1CId = $manager1CId;
        $this->agent1CId = $agent1CId;
        $this->fcCbrId = $fcCbrId;
        $this->status = $status->getValue();
        $this->contractNumber = $contractNumber;
        $this->contractDate = $contractDate;

        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function update(
        FullName $fullName,
        EdrpouInn $edrpouInn,
        Type $type,
        NktId $nktId,
        Manager1CId $manager1CId,
        Agent1CId $agent1CId,
        FcCbrId $fcCbrId,
        Status $status
    ): self {
        $this->fullName = $fullName;
        $this->edrpouInn = $edrpouInn;
        $this->type = $type->getValue();
        $this->nktId = $nktId->getValue();
        $this->manager1CId = $manager1CId;
        $this->agent1CId = $agent1CId;
        $this->fcCbrId = $fcCbrId;
        $this->status = $status->getValue();

        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    /**
     * @return Company
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @return Register[]|PersistentCollection
     */
    public function getRegisterToken()
    {
        return $this->registerToken;
    }

    public function getClient1CId(): string
    {
        return $this->client1CId;
    }

    public function getFcCbrId()
    {
        return $this->fcCbrId;
    }

    public function getFcCbrIdForPc(): string
    {
        $value = $this->fcCbrId;
        if (strlen($value) < 12) {
            $value = str_repeat('0', 12 - strlen($value)).$value;
        }

        return $value;
    }

    public function getClientPcId(): int
    {
        return $this->nktId;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(FullName $fullName)
    {
        $this->fullName = $fullName;
    }

    public function getEdrpouInn(): ?string
    {
        return $this->edrpouInn;
    }

    public function setEdrpouInn(EdrpouInn $edrpouInn)
    {
        $this->edrpouInn = $edrpouInn;
    }

    public function getSotaToken(): ?string
    {
        return $this->sotaToken;
    }

    public function setSotaToken(string $sotaToken)
    {
        $this->sotaToken = $sotaToken;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getManager1CId(): string
    {
        return $this->manager1CId;
    }

    public function getContractNumber(): string
    {
        return (string) $this->contractNumber;
    }

    public function getContractDate(): ?\DateTimeInterface
    {
        return $this->contractDate;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
}
