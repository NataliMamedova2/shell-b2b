<?php

namespace FilesUploader\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class NameGenerator implements NameGeneratorInterface
{

    public function generate(UploadedFile $file, array $params = []): string
    {
        return sprintf(
            '%s_%d.%s',
            sha1($file->getClientOriginalName()),
            time(),
            $file->getClientOriginalExtension()
        );
    }
}
