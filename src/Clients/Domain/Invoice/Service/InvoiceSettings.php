<?php

namespace App\Clients\Domain\Invoice\Service;

interface InvoiceSettings
{
    /**
     * Податок на додану вартість.
     */
    public function getValueAddedTax(): int;

    public function getInvoiceValidDays(): int;
}
