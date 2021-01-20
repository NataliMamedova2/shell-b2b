<?php

namespace App\Media\Action\Api\UploadIcon;

use FilesUploader\HttpFoundation\Base64EncodedFile;
use FilesUploader\HttpFoundation\UploadedBase64EncodedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use App\Media\Validator\Constraints\Icon as IconAssert;

final class InputModel
{
    /**
     * @var UploadedFile
     *
     * @Assert\NotBlank()
     * @IconAssert(
     *     extensions={"png", "svg"}
     * )
     * @Assert\File(
     *      maxSize="10M"
     * )
     */
    private $file;

    /**
     * @param Request $request
     * @return InputModel
     */
    public static function createFromRequest(Request $request): self
    {
        $files = $request->files->all();
        $inputData = json_decode($request->getContent(), true);
        $inputData = $inputData ?? $files;

        $file = $inputData['file'] ?? null;
        if (is_string($file)) {
            $file = new UploadedBase64EncodedFile(new Base64EncodedFile($file));
        }

        $entity = new self();
        $entity->file = $file;

        return $entity;
    }

    /**
     * @return UploadedFile
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }
}
