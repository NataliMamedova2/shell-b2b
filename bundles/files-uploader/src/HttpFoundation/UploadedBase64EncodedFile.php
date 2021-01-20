<?php

namespace FilesUploader\HttpFoundation;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedBase64EncodedFile extends UploadedFile
{
    /**
     * @param Base64EncodedFile $file
     * @param string            $originalName
     * @param null              $mimeType
     */
    public function __construct(Base64EncodedFile $file, $originalName = '', $mimeType = null)
    {
        parent::__construct($file->getPathname(), $originalName ?: $file->getFilename(), $mimeType, null, true);
    }
}
