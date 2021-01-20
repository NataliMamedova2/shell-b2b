<?php

namespace App\Export\Domain\Export\ValueObject;

use Domain\Exception\InvalidArgumentException;
use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class File
{
    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $extension;

    /**
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @ORM\Column(type="string")
     */
    private $sourcePath;

    public function __construct(string $path, string $name, string $extension, $size = 0)
    {
        Assert::notEmpty($path);
        Assert::notEmpty($name);
        Assert::notEmpty($extension);

        $filteredValue = parse_url($path, PHP_URL_PATH);
        if (null === $filteredValue || strlen($filteredValue) != strlen($path)) {
            throw new InvalidArgumentException($path, ['string (valid url path)']);
        }

        $this->sourcePath = $path;
        $this->name = $name;
        $this->extension = $extension;
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }
}
