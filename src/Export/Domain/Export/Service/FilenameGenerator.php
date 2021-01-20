<?php

namespace App\Export\Domain\Export\Service;

interface FilenameGenerator
{
    public function generate(string $ext): Filename;
}
