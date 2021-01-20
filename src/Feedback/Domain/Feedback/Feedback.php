<?php

namespace App\Feedback\Domain\Feedback;

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\User\User;
use App\Feedback\Domain\Feedback\ValueObject\Comment;
use App\Feedback\Domain\Feedback\ValueObject\FeedbackCategory;
use App\Feedback\Domain\Feedback\ValueObject\FeedbackId;
use App\Feedback\Domain\Feedback\ValueObject\FullName;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="feedback")
 */
class Feedback
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
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $email;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Clients\Domain\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(name="category", type="string", length=255, nullable=false)
     */
    private $category;

    /**
     * @var string
     * @ORM\Column(name="comment", type="text", nullable=false)
     */
    private $comment;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    private function __construct(
        FeedbackId $id,
        Email $email,
        User $user,
        FullName $name,
        FeedbackCategory $category,
        Comment $comment
    ) {
        $this->id = $id;
        $this->email = $email->getValue();
        $this->user = $user;
        $this->name = $name->getValue();
        $this->category = $category->getValue();
        $this->comment = $comment->getValue();
    }

    public static function create(
        FeedbackId $id,
        Email $email,
        User $user,
        FullName $name,
        FeedbackCategory $category,
        Comment $comment
    ): self {
        $self = new self($id, $email, $user, $name, $category, $comment);

        $self->createdAt = new \DateTimeImmutable();

        return $self;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory()
    {
        if (!$this->category instanceof FeedbackCategory) {
            $this->category = new FeedbackCategory($this->category);
        }

        return $this->category;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
