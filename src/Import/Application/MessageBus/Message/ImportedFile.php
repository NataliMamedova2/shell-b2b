<?php

declare(strict_types=1);

namespace App\Import\Application\MessageBus\Message;

use App\Import\Domain\Import\File\ValueObject\FileId;

final class ImportedFile
{
    /**
     * @var FileId
     */
    private $fileId;

    public function __construct(FileId $fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * @return FileId
     */
    public function getFileId(): FileId
    {
        return $this->fileId;
    }
}
