<?php

namespace App\Users\Domain\User;

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Client\ValueObject\Manager1CId;
use App\Users\Domain\User\ValueObject\Avatar;
use App\Users\Domain\User\ValueObject\FullName;
use App\Users\Domain\User\ValueObject\Phone;
use App\Users\Domain\User\ValueObject\Role;
use App\Users\Domain\User\ValueObject\Status;
use App\Users\Domain\User\ValueObject\UserId;
use App\Users\Domain\User\ValueObject\Username;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"email", "username"})},
 *      indexes={@ORM\Index(columns={"email", "username"})}
 * )
 */
class User implements UserInterface
{
    /**
     * @var UserId
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(name="manager_1c_id", type="string", length=12, nullable=true)
     */
    private $manager1CId;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", nullable=false)
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var Avatar
     *
     * @ORM\Embedded(class="\App\Users\Domain\User\ValueObject\Avatar")
     */
    private $avatar;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=13, nullable=true)
     */
    private $phone;

    /**
     * @var array
     * @ORM\Column(type="json", options={"jsonb": true})
     */
    private $roles = [];

    /**
     * @var int
     * @ORM\Column(name="status", type="smallint", nullable=false, options={"default" : 0})
     */
    private $status;

    /**
     * @var \DateTimeInterface|null
     * @ORM\Column(name="last_logged_at", type="datetime", nullable=true)
     */
    private $lastLoggedAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="sota_token", type="string", length=33)
     */
    private $sotaToken;

    private function __construct(
        UserId $id,
        Email $email,
        Username $username,
        string $name,
        Role $role,
        Status $status
    ) {
        $this->id = $id;
        $this->email = $email->getValue();
        $this->username = $username->getValue();
        $this->name = $name;
        $this->roles = [$role->getValue()];
        $this->status = $status->getValue();
    }

    public static function create(
        UserId $id,
        Email $email,
        Username $username,
        FullName $name,
        Role $role,
        Status $status,
        Phone $phone = null,
        Avatar $avatar = null,
        Manager1CId $manager1CId = null
    ): self {
        $self = new self($id, $email, $username, $name, $role, $status);

        $self->phone = $phone->getValue();
        $self->avatar = $avatar;
        $self->manager1CId = $manager1CId;

        $self->createdAt = new \DateTimeImmutable();
        $self->updatedAt = new \DateTimeImmutable();

        return $self;
    }

    public function update(
        Email $email,
        Username $username,
        FullName $name,
        Role $role,
        Status $status,
        Phone $phone = null,
        Avatar $avatar = null,
        Manager1CId $manager1CId = null
    ): self {
        $this->email = $email->getValue();
        $this->username = $username->getValue();
        $this->name = $name->getValue();
        $this->roles = [$role->getValue()];
        $this->status = $status->getValue();

        $this->phone = $phone->getValue();
        $this->avatar = $avatar;
        $this->manager1CId = $manager1CId;

        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSotaToken(): ?string
    {
        return $this->sotaToken;
    }

    public function setSotaToken(string $sotaToken)
    {
        $this->sotaToken = $sotaToken;
    }

    /**
     * @return Avatar
     */
    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getRole(): string
    {
        return current($this->roles);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return (new Status($this->status))->isActive();
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword(): ?string
    {
        return (string) $this->password;
    }

    public function changePassword(string $password): void
    {
        $this->password = $password;
    }

    public function getLastLoggedAt(): ?\DateTimeInterface
    {
        return $this->lastLoggedAt;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function loggedIn(\DateTimeInterface $dateTime): void
    {
        $this->lastLoggedAt = $dateTime;
    }

    public function getManager1CId(): ?string
    {
        return $this->manager1CId;
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
