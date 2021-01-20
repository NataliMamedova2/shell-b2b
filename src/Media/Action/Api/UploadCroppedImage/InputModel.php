<?php

namespace App\Media\Action\Api\UploadCroppedImage;

use FilesUploader\HttpFoundation\Base64EncodedFile;
use FilesUploader\HttpFoundation\UploadedBase64EncodedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class InputModel
{
    /**
     * @var UploadedFile
     *
     * @Assert\NotBlank()
     * @Assert\File(
     *      mimeTypes={
     *          "image/png",
     *          "image/x-png",
     *          "image/jpg",
     *          "image/jpeg"
     *      },
     *      maxSize="10M"
     * )
     */
    private $file;

    private $cropData = [];

    /**
     * @param Request $request
     * @return InputModel
     */
    public static function createFromRequest(Request $request): self
    {
        $files = $request->files->all();
        $inputData = json_decode($request->getContent(), true);
        $inputData = $inputData ?? $files;

        if (!isset($inputData['file'])) {
            throw new \InvalidArgumentException('Required property "file" not found');
        }

        $file = $inputData['file'];
        if (is_string($file)) {
            $file = new UploadedBase64EncodedFile(new Base64EncodedFile($file));
        }

        $cropData = $inputData['cropData'] ?? [];
        array_walk($cropData, static function (&$v) {
            $v = (float)$v;
        });

        $entity = new self();
        $entity->file = $file;
        $entity->cropData = $cropData;

        return $entity;
    }

    /**
     * @return UploadedFile
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @return array
     */
    public function getCropData(): array
    {
        return $this->cropData;
    }
}
