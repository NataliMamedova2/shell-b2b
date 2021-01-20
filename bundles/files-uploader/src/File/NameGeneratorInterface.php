<?php

namespace FilesUploader\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface NameGeneratorInterface
{
    public function generate(UploadedFile $file, array $params = []);
}
