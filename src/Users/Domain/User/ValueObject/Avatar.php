<?php

declare(strict_types=1);

namespace App\Users\Domain\User\ValueObject;

use App\Application\Domain\Exception\InvalidArgumentException;
use App\Media\Model\CropperInterface;
use App\Media\Model\MediaInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class Avatar implements MediaInterface, CropperInterface
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $path;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $fileName;

    /**
     * @var array
     * @ORM\Column(type="json", options={"jsonb": true}, nullable=true)
     */
    private $cropData;

    public function __construct(string $path, string $fileName, ?array $cropData = [])
    {
        $filteredValue = parse_url($path, PHP_URL_PATH);
        if (null === $filteredValue || strlen($filteredValue) != strlen($path)) {
            throw new InvalidArgumentException($path, ['string (valid url path)']);
        }

        $this->path = $path;
        $this->fileName = $fileName;
        $this->cropData = $cropData;
    }

    public function fromArray(array $data): self
    {
        $path = $data['path'] ?? '';
        $fileName = $data['fileName'] ?? '';
        $cropData = $data['cropData'] ?? '';

        return new self($path, $fileName, $cropData);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getCropData(): array
    {
        return $this->cropData;
    }

    public function getFile(): string
    {
        return $this->path.$this->fileName;
    }
}
