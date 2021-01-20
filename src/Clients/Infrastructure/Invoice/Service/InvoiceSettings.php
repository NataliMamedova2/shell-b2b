<?php

namespace App\Clients\Infrastructure\Invoice\Service;

use App\Clients\Domain\Invoice\Service\InvoiceSettings as DomainInvoiceSettings;
use App\Clients\Domain\ShellInformation\ShellInformation;
use Infrastructure\Exception\InvalidArgumentException;
use Infrastructure\Interfaces\Repository\Repository;

final class InvoiceSettings implements DomainInvoiceSettings
{
    private $valueAddedTax;

    private $invoiceValidDays;

    public function __construct(Repository $shellInfoRepository)
    {
        /** @var ShellInformation $shellInfo */
        $shellInfo = $shellInfoRepository->find([]);

        if (!$shellInfo instanceof ShellInformation) {
            throw new InvalidArgumentException('ShellInformation not found');
        }

        $this->valueAddedTax = (int) $shellInfo->getValueAddedTax();
        $this->invoiceValidDays = (int) $shellInfo->getInvoiceValidDays();
    }

    public function getValueAddedTax(): int
    {
        return $this->valueAddedTax;
    }

    public function getInvoiceValidDays(): int
    {
        return $this->invoiceValidDays;
    }
}
