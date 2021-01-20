<?php

namespace App\Import\Domain\Import\File;

use App\Import\Domain\Import\File\ValueObject\FileId;
use App\Import\Domain\Import\File\ValueObject\MetaData;
use App\Import\Domain\Import\File\ValueObject\Status\CopiedStatus;
use App\Import\Domain\Import\File\ValueObject\Status\DoneStatus;
use App\Import\Domain\Import\File\ValueObject\Status\ErrorStatus;
use App\Import\Domain\Import\File\ValueObject\Status\FailedStatus;
use App\Import\Domain\Import\File\ValueObject\Status\InProgressStatus;
use App\Import\Domain\Import\File\ValueObject\Status\StartedStatus;
use App\Import\Domain\Import\Import;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="import_files")
 */
class File
{
    /**
     * @var FileId
     *
     * @ORM\Id()
     * @ORM\Column(unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Import\Domain\Import\Import", inversedBy="files", cascade={"persist"})
     */
    private $import;

    /**
     * @var Result
     *
     * @ORM\Embedded(class="\App\Import\Domain\Import\File\Result", columnPrefix=false)
     */
    private $result;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $extension;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $size;

    /**
     * @ORM\Column(type="json", options={"jsonb": true}, nullable=true)
     */
    private $sourceFileMetaData;

    /**
     * @ORM\Column(type="json", options={"jsonb": true}, nullable=true)
     */
    private $destFileMetaData;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    private function __construct(FileId $id, Import $import, string $fileName, string $extension, int $size)
    {
        $this->id = $id;
        $this->import = $import;
        $this->fileName = $fileName;
        $this->extension = $extension;
        $this->size = $size;

        $this->status = (new StartedStatus())->getValue();
    }

    public static function create(
        Import $import,
        string $fileName,
        string $extension,
        int $size,
        MetaData $sourceFileMetaData,
        DateTimeInterface $createdAt
    ): self {
        $self = new self(FileId::next(), $import, $fileName, $extension, $size);

        $self->createdAt = $createdAt;
        $self->sourceFileMetaData = $sourceFileMetaData->toArray();

        return $self;
    }

    public function setDestFileMetaData(MetaData $destFileMetaData): void
    {
        $this->destFileMetaData = $destFileMetaData->toArray();
        $this->status = (new CopiedStatus())->getValue();
    }

    public function getDestFileMetaData(): MetaData
    {
        if (is_array($this->destFileMetaData)) {
            return MetaData::fromArray($this->destFileMetaData);
        }

        return $this->destFileMetaData;
    }

    public function inProgress(): void
    {
        $this->status = (new InProgressStatus())->getValue();
    }

    public function error(string $message = null): void
    {
        $this->status = (new ErrorStatus())->getValue();
        $this->message = $message;

        $statuses = ['done', 'error', 'failed'];
        $itLastFile = true;
        foreach ($this->getImport()->getFiles() as $file) {
            if (!in_array($file->getStatus(), $statuses)) {
                $itLastFile = false;
            }
        }

        if (true === $itLastFile) {
            $this->getImport()->done(new \DateTimeImmutable());
        }
    }

    public function failed(string $message = null): void
    {
        $this->status = (new FailedStatus())->getValue();
        $this->message = $message;

        $statuses = ['done', 'error', 'failed'];
        $itLastFile = true;
        foreach ($this->getImport()->getFiles() as $file) {
            if (!in_array($file->getStatus(), $statuses)) {
                $itLastFile = false;
            }
        }

        if (true === $itLastFile) {
            $this->getImport()->done(new \DateTimeImmutable());
        }
    }

    public function done(Result $result): void
    {
        $status = new DoneStatus();
        if ($result->getErrorCount() > 0) {
            $status = new ErrorStatus();
        }
        $this->result = $result;
        $this->status = $status->getValue();

        $doneStatus = (new DoneStatus())->getValue();
        $completed = true;
        foreach ($this->getImport()->getFiles() as $file) {
            if ($file->getStatus() !== $doneStatus) {
                $completed = false;
            }
        }

        if (true === $completed) {
            $this->getImport()->done(new \DateTimeImmutable());
        }
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getImport(): Import
    {
        return $this->import;
    }

    public function getResult(): Result
    {
        return $this->result;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getFullName(): string
    {
        return $this->fileName.'.'.$this->extension;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
