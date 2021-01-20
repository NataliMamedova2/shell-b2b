<?php

declare(strict_types=1);

namespace App\Media\Model;

interface MediaInterface
{
    public function getPath();

    public function getFileName();

    public function getFile();
}
