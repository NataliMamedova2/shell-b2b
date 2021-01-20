<?php

namespace FilesUploader\Domain\Storage;

use FilesUploader\Domain\Storage\ValueObject\Id;
use FilesUploader\Domain\Storage\ValueObject\IpAddress;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="files_storage")
 */
class File
{
    /**
     * @var Id
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

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
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=5, nullable=false)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $originalName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=40, nullable=false)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $size;

    /**
     * @var array|null
     *
     * @ORM\Column(type="json", nullable=true, options={"jsonb": true})
     */
    private $metaInfo;

    /**
     * @var IpAddress
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $uploadedIp;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $uploadedAt;

    /**
     * File constructor.
     *
     * @param Id         $id
     * @param string     $fileName
     * @param string     $path
     * @param string     $extension
     * @param string     $originalName
     * @param string     $type
     * @param int        $size
     * @param array|null $metaInfo
     * @param IpAddress  $uploadedIp
     */
    private function __construct(
        Id $id,
        string $fileName,
        string $path,
        string $extension,
        string $originalName,
        string $type,
        int $size,
        ?array $metaInfo,
        IpAddress $uploadedIp
    ) {
        $this->id = $id;
        $this->fileName = $fileName;
        $this->path = $path;
        $this->extension = $extension;
        $this->originalName = $originalName;
        $this->type = $type;
        $this->size = $size;
        $this->metaInfo = $metaInfo;
        $this->uploadedIp = $uploadedIp;
    }

    /**
     * @param Id         $id
     * @param string     $fileName
     * @param string     $path
     * @param string     $extension
     * @param string     $originalName
     * @param string     $type
     * @param int        $size
     * @param array|null $metaInfo
     * @param IpAddress  $uploadedIp
     *
     * @return File
     *
     * @throws \Exception
     */
    public static function create(
        Id $id,
        string $fileName,
        string $path,
        string $extension,
        string $originalName,
        string $type,
        int $size,
        ?array $metaInfo,
        IpAddress $uploadedIp
    ): self {
        $file = new self($id, $fileName, $path, $extension, $originalName, $type, $size, $metaInfo, $uploadedIp);
        $file->uploadedAt = new \DateTimeImmutable();

        return $file;
    }
}
