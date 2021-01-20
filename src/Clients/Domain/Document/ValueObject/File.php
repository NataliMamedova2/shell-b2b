<?php

namespace App\Clients\Domain\Document\ValueObject;

use App\Application\Domain\Exception\InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
final class File
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $path;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $extension;

    public function __construct(string $path, string $fileName, string $ext)
    {
        Assert::notEmpty($path);
        Assert::notEmpty($fileName);
        Assert::notEmpty($ext);

        if ('/' !== substr($path, -1)) {
            $path = $path.'/';
        }
        $this->path = $path;
        $this->name = basename($fileName, '.'.$ext);
        $this->extension = $ext;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameWithExtension(): string
    {
        return $this->name.'.'.$this->extension;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getFile(): string
    {
        return $this->path.$this->getNameWithExtension();
    }
}
