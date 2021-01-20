<?php

namespace App\Partners\Domain\User;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\User\ValueObject\Name;
use App\Clients\Domain\User\ValueObject\Status;
use App\Clients\Domain\User\ValueObject\Username;
use App\Partners\Domain\Partner\Partner;
use App\Partners\Domain\User\ValueObject\Role;
use App\Partners\Domain\User\ValueObject\UserId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="partner_users",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"email", "username"})},
 *      indexes={@ORM\Index(columns={"email", "username"})}
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Partners\Domain\Partner\Partner", cascade={"persist"})
     * @ORM\JoinColumn(name="partner_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $partner;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $email;

    /**
     * @ORM\Embedded(class="App\Clients\Domain\User\ValueObject\Name", columnPrefix=false)
     */
    private $name;

    /**
     * @ORM\Column(name="phone", type="string", length=13, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(name="password", type="string", nullable=false)
     */
    private $password;

    /**
     * @var array
     * @ORM\Column(type="json", options={"jsonb": true})
     */
    private $roles = [];

    /**
     * @ORM\Column(name="status", type="smallint", nullable=false, options={"default": 0})
     */
    private $status;
    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="last_logged_at", type="datetime_immutable", nullable=true)
     */
    private $lastLoggedAt;

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
        UserId $id,
        Partner $partner,
        Email $email,
        Username $username,
        Name $name,
        Role $role
    ) {
        $this->id = $id;
        $this->partner = $partner;
        $this->email = $email;
        $this->username = $username;
        $this->name = $name;
        $this->roles = [$role->getValue()];
        $this->status = Status::active()->getValue();
    }

    public static function create(
        UserId $id,
        Partner $partner,
        Email $email,
        Username $username,
        string $password,
        Name $name,
        Role $role,
        Phone $phone = null
    ): self {
        $self = new self($id, $partner, $email, $username, $name, $role);

        $self->phone = $phone;
        $self->password = $password;

        $dateTime = new \DateTimeImmutable();
        $self->createdAt = $dateTime;
        $self->updatedAt = $dateTime;

        return $self;
    }

    public function update(
        Email $email,
        Username $username,
        Name $name,
        Role $role,
        Phone $phone = null
    ) {
        $this->email = $email;
        $this->username = $username;
        $this->name = $name;
        $this->roles = [$role->getValue()];
        $this->phone = $phone;
    }

    public function updateProfile(
        Email $email,
        Username $username,
        Name $name,
        Phone $phone = null
    ) {
        $this->email = $email;
        $this->username = $username;
        $this->name = $name;
        $this->phone = $phone;
    }

    public function changePassword(string $password): void
    {
        $this->password = $password;
    }

    public function changeStatus(Status $status): void
    {
        $this->status = $status->getValue();
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getPartner(): Partner
    {
        return $this->partner;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return (string) $this->phone;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getRole(): string
    {
        return current($this->roles);
    }

    public function getRoleName(): string
    {
        return (new Role($this->getRole()))->getName();
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getStatusName(): string
    {
        return (new Status($this->status))->getName();
    }

    public function isActive(): bool
    {
        return (new Status($this->status))->isActive();
    }

    public function loggedIn(\DateTimeInterface $lastLoggedAt): void
    {
        $this->lastLoggedAt = $lastLoggedAt;
    }

    public function getLastLoggedAt(): ?\DateTimeInterface
    {
        return $this->lastLoggedAt;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }
}
