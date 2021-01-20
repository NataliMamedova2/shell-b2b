<?php

namespace App\Clients\Domain\Document\Service;

use App\Clients\Domain\Document\ValueObject\File;
use App\Clients\Domain\Invoice\Invoice;

interface InvoiceFileService
{
    public function create(Invoice $invoice): File;
}
