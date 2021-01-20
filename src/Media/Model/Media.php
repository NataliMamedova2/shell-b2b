<?php

namespace App\Media\Model;

final class Media implements MediaInterface, CropperInterface
{

    private $path;

    private $fileName;

    private $cropData = [];

    public static function createFromArray(array $data)
    {
        $entity = new self();

        $entity->path = $data['path'] ?? '';
        $entity->fileName = $data['fileName'] ?? '';
        $entity->cropData = $data['cropData'] ?? [];

        return $entity;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @return array
     */
    public function getCropData(): array
    {
        return $this->cropData;
    }

    public function getFile(): string
    {
        return $this->path . $this->fileName;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @param array $cropData
     */
    public function setCropData(array $cropData): void
    {
        $this->cropData = $cropData;
    }
}
