<?php

namespace App\Clients\Infrastructure\Document\Service;

use App\Clients\Domain\Document\Service\UploadDocumentFileService as DomainUploadDocumentFileService;
use App\Clients\Domain\Document\ValueObject\File;
use FilesUploader\File\PathGeneratorInterface;
use League\Flysystem\FileExistsException;
use League\Flysystem\FilesystemInterface;

final class UploadDocumentFileService implements DomainUploadDocumentFileService
{
    /**
     * @var PathGeneratorInterface
     */
    private $pathGenerator;

    /**
     * @var FilesystemInterface
     */
    private $defaultFilesystem;

    public function __construct(
        PathGeneratorInterface $pathGenerator,
        FilesystemInterface $defaultFilesystem
    ) {
        $this->pathGenerator = $pathGenerator;
        $this->defaultFilesystem = $defaultFilesystem;
    }

    /**
     * @param mixed|resource $resource
     * @param string $namePrefix
     * @param string $extension
     *
     * @return File
     * @throws FileExistsException
     */
    public function upload($resource, string $namePrefix, string $extension): File
    {
        if (false === is_resource($resource)) {
            throw new \InvalidArgumentException('Invalid file resource');
        }
        $nameWithExtension = $this->generateName($namePrefix).'.'.$extension;
        $path = $this->pathGenerator->generate($nameWithExtension, ['pathPrefix' => 'documents']);

        $result = $this->defaultFilesystem->writeStream($path.$nameWithExtension, $resource);
        if (false === $result) {
            throw new \RuntimeException('Upload file error');
        }

        return new File($path, $nameWithExtension, $extension);
    }

    private function generateName(string $prefixName)
    {
        return sprintf(
            '%s_%d',
            $prefixName,
            time()
        );
    }
}
