<?php

namespace FilesUploader\Domain\Storage\Repository;

use FilesUploader\Domain\Storage\File;

interface RepositoryInterface
{
    public function add(File $entity);
}
