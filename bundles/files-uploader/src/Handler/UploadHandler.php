<?php

namespace FilesUploader\Handler;

use FilesUploader\Domain\Storage\Service\Create;
use FilesUploader\File\NameGeneratorInterface;
use FilesUploader\File\PathGeneratorInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadHandler
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var PathGeneratorInterface
     */
    private $pathGenerator;

    /**
     * @var NameGeneratorInterface
     */
    private $nameGenerator;

    /**
     * @var Create\Handler
     */
    private $createHandler;

    public function __construct(
        FilesystemInterface $filesystem,
        PathGeneratorInterface $pathGenerator,
        NameGeneratorInterface $nameGenerator,
        Create\Handler $createHandler
    ) {
        $this->filesystem = $filesystem;
        $this->pathGenerator = $pathGenerator;
        $this->nameGenerator = $nameGenerator;
        $this->createHandler = $createHandler;
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws \Exception
     */
    public function handle(UploadedFile $file, $params = []): array
    {
        $name = $this->nameGenerator->generate($file);
        $path = $this->pathGenerator->generate($name, $params);

        $stream = fopen($file->getRealPath(), 'rb+');
        $this->filesystem->putStream($path.$name, $stream, [
            'mimetype' => $file->getMimeType(),
        ]);

        if (is_resource($stream)) {
            fclose($stream);
        }

        $data = [
            'fileName' => $name,
            'originalName' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'path' => $path,
            'type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'metaInfo' => $this->getFileMetaData($file),
        ];

        $this->createHandler->handle(Create\Command::formArray($data));

        return $data;
    }

    private function getFileMetaData(UploadedFile $file): array
    {
        $fileData = [
            'originalName' => $file->getClientOriginalName(),
            'originalExtension' => $file->getClientOriginalExtension(),
            'mimeType' => $file->getClientMimeType(),
        ];

        $imageSize = @getimagesize($file->getRealPath());
        if (is_array($imageSize)) {
            if (is_int($imageSize[0]) && is_int($imageSize[1])) {
                $fileData['width'] = $imageSize[0];
                $fileData['height'] = $imageSize[1];
            }
            if (isset($imageSize['bits'])) {
                $fileData['bits'] = $imageSize['bits'];
            }
            if (isset($imageSize['channels'])) {
                $fileData['channels'] = $imageSize['channels'];
            }
        }

        return $fileData;
    }
}
