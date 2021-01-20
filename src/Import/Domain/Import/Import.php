<?php

namespace App\Import\Domain\Import;

use App\Import\Domain\Import\File\File;
use App\Import\Domain\Import\ValueObject\ImportId;
use App\Import\Domain\Import\ValueObject\Status\DoneStatus;
use App\Import\Domain\Import\ValueObject\Status\FailedStatus;
use App\Import\Domain\Import\ValueObject\Status\ProcessingStatus;
use App\Import\Domain\Import\ValueObject\Status\StartedStatus;
use App\Import\Domain\Import\ValueObject\Status\Status;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="import")
 */
class Import
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\App\Import\Domain\Import\File\File", mappedBy="import", cascade={"persist"})
     */
    private $files;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $startedAt;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $endedAt;

    private function __construct(ImportId $importId, \DateTimeInterface $startedAt)
    {
        $this->id = $importId->getId();
        $this->status = (new StartedStatus())->getValue();
        $this->startedAt = $startedAt;
        $this->files = new ArrayCollection();
    }

    public static function start(\DateTimeInterface $startedAt): self
    {
        return new self(ImportId::next(), $startedAt);
    }

    public function done(\DateTimeInterface $dateTime): void
    {
        $this->changeStatus(new DoneStatus());

        $this->endedAt = $dateTime;
    }

    private function changeStatus(Status $status): void
    {
        if ($this->status instanceof Status) {
            $this->status->ensureCanBeChangedTo($status);
        }
        $this->status = $status->getValue();
    }

    public function processing(): void
    {
        $this->changeStatus(new ProcessingStatus());
    }

    public function failed(): void
    {
        $this->changeStatus(new FailedStatus());
    }

    /**
     * @return ArrayCollection|File[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function addFile(File $file): void
    {
        $this->files->add($file);
    }

    /**
     * @return \DateTimeInterface
     */
    public function getEndedAt(): \DateTimeInterface
    {
        return $this->endedAt;
    }
}
