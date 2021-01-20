<?php

namespace App\Api\Action\Api\V1\Transactions\Card\CreateReportAction\Service;

use PhpOffice\PhpSpreadsheet\Writer as Writer;

interface TransactionsXlsWriter
{
    public function create(array $transactions, array $replenishmentTransactions, array $fuelTypes, string $company, array $filterData = [], array $companies = []): Writer\Xls;
}
