<?php

namespace App\Media\Model;

final class Logo implements MediaInterface
{

    private $path;

    private $fileName;

    public static function createFromArray(array $data)
    {
        $entity = new self();

        $entity->path = $data['path'] ?? '';
        $entity->fileName = $data['fileName'] ?? '';

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
}
