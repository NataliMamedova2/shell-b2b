<?php

namespace App\Clients\Domain\Document\Service;

use App\Clients\Domain\Document\ValueObject\File;

interface UploadDocumentFileService
{
    public function upload($resource, string $namePrefix, string $extension): File;
}
