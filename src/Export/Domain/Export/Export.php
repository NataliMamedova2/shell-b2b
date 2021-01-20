<?php

namespace App\Export\Domain\Export;

use App\Application\Domain\ValueObject\IdentityId;
use App\Export\Domain\Export\ValueObject\File;
use App\Export\Domain\Export\ValueObject\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="export")
 */
class Export
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
     * @ORM\Embedded(class="App\Export\Domain\Export\ValueObject\File")
     */
    private $file;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    public function __construct(IdentityId $id, File $file, Type $type, \DateTimeInterface $dateTime)
    {
        $this->id = $id->getId();
        $this->file = $file;
        $this->type = $type->getValue();
        $this->createdAt = $dateTime;
    }
}
