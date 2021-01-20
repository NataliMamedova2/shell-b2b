<?php

namespace App\Clients\Domain\User;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\User\ValueObject\Name;
use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\Status;
use App\Clients\Domain\User\ValueObject\Token;
use App\Clients\Domain\User\ValueObject\UserId;
use App\Clients\Domain\User\ValueObject\Username;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="company_users",
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
     * @ORM\ManyToOne(targetEntity="App\Clients\Domain\Company\Company", cascade={"persist"})
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $company;

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
     * @ORM\Embedded(class="App\Clients\Domain\User\ValueObject\Token", columnPrefix=false)
     */
    private $restorePassToken;

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
        Company $company,
        Email $email,
        Username $username,
        Name $name,
        Role $role
    ) {
        $this->id = $id;
        $this->company = $company;
        $this->email = $email;
        $this->username = $username;
        $this->name = $name;
        $this->roles = [$role->getValue()];
        $this->status = Status::active()->getValue();
    }

    public static function create(
        UserId $id,
        Company $company,
        Email $email,
        Username $username,
        string $password,
        Name $name,
        Role $role,
        Phone $phone = null
    ): self {
        $self = new self($id, $company, $email, $username, $name, $role);

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

    /**
     * @param string $password
     */
    public function changePassword(string $password): void
    {
        $this->password = $password;
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
        return (string) $this->id;
    }

    /**
     * @return Company
     */
    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return string
     */
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

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getRole(): string
    {
        return current($this->roles);
    }

    /**
     * @return string
     */
    public function getRoleName(): string
    {
        return (new Role($this->getRole()))->getName();
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return (new Status($this->status))->getName();
    }

    public function isActive(): bool
    {
        return (new Status($this->status))->isActive();
    }

    /**
     * @param \DateTimeInterface $lastLoggedAt
     */
    public function loggedIn(\DateTimeInterface $lastLoggedAt): void
    {
        $this->lastLoggedAt = $lastLoggedAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getLastLoggedAt(): ?\DateTimeInterface
    {
        return $this->lastLoggedAt;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeInterface
     */
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

    public function setRestoreToken(Token $token)
    {
        $this->restorePassToken = $token;
    }

    public function getRestoreToken(): Token
    {
        return $this->restorePassToken;
    }

    public function clearRestoreToken()
    {
        $this->restorePassToken = null;
    }
}
