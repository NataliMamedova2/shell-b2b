<?php

namespace App\Clients\Domain\Document\Service;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Document\ValueObject\File;

interface ActCheckingFileService
{
    public function create(Client $client, \DateTimeInterface $dateFrom, \DateTimeInterface $dateTo): File;
}
