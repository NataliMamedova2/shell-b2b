<?php

namespace App\Import\Application\FileDataSaver;

interface FileDataSaverInterface
{
    public function supportedFile(string $extension): bool;

    public function recordsChunkSize(): int;

    public function handle(\Iterator $data): ResultInterface;
}
