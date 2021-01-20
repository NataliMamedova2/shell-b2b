<?php

namespace App\Export\Infrastructure\Service;

use App\Export\Domain\Export\Service\Filename as Basename;

final class Filename implements Basename
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $extension;

    public function __construct(string $name, string $extension)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Argument "name" is required');
        }
        if (empty($extension)) {
            throw new \InvalidArgumentException('Argument "extension" is required');
        }

        $this->name = $name;
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function getFilename(): string
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
    public function getBasename(): string
    {
        return sprintf('%s.%s', $this->name, $this->extension);
    }
}
