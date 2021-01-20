<?php

namespace App\Clients\Infrastructure\Invoice\Service;

use App\Clients\Domain\Invoice\Service\NumberGenerator as DomainNumberGenerator;
use App\Clients\Domain\ShellInformation\ShellInformation;
use Infrastructure\Exception\InvalidArgumentException;
use Infrastructure\Interfaces\Repository\Repository;

final class NumberGenerator implements DomainNumberGenerator
{
    private $startIndex = 20000;

    /**
     * @var Repository
     */
    private $shellInfoRepository;

    /**
     * @var Repository
     */
    private $invoiceRepository;

    public function __construct(Repository $shellInfoRepository, Repository $invoiceRepository)
    {
        $this->shellInfoRepository = $shellInfoRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function next(): string
    {
        /** @var ShellInformation $shellInfo */
        $shellInfo = $this->shellInfoRepository->find([]);

        if (!$shellInfo instanceof ShellInformation) {
            throw new InvalidArgumentException('ShellInformation not found');
        }

        $prefix = $shellInfo->getInvoiceNumberPrefix();
        $countInvoices = $this->invoiceRepository->count();

        $numberValue = $this->startIndex + $countInvoices;
        if (strlen($numberValue) < 6) {
            $numberValue = str_repeat('0', 6 - strlen($numberValue)).$numberValue;
        }

        return $prefix.$numberValue;
    }
}
