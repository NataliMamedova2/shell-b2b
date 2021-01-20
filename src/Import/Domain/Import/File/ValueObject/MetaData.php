<?php

namespace App\Import\Domain\Import\File\ValueObject;

final class MetaData
{
    private $type;
    private $path;
    private $timestamp;
    private $size;

    public function __construct(string $type, string $path, int $size, int $timestamp)
    {
        $this->type = $type;
        $this->path = $path;
        $this->size = $size;
        $this->timestamp = $timestamp;
    }

    public static function fromArray(array $data): self
    {
        $type = $data['type'] ?? '';
        $path = $data['path'] ?? '';
        $size = $data['size'] ?? 0;
        $timestamp = $data['timestamp'] ?? 0;

        return new self($type, $path, $size, $timestamp);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }


    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
