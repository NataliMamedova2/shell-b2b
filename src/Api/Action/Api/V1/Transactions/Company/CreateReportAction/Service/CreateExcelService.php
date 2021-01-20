<?php

namespace App\Api\Action\Api\V1\Transactions\Company\CreateReportAction\Service;

use App\Clients\Domain\Transaction\Company\Transaction;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer as Writer;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateExcelService
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var Spreadsheet */
    private $spreadSheet;

    /** @var Worksheet */
    private $activeSheet;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->spreadSheet = new Spreadsheet();
        $this->activeSheet = $this->spreadSheet->getActiveSheet();
    }

    public function create(array $transactions, string $company, array $filteredData = []): Writer\Xls
    {
        $this->prepareSheet();
        $this->makeHeader($company, $filteredData);
        $lastRow = $this->addTable(7, 'Транзакції', $transactions);

        return new Writer\Xls($this->spreadSheet);
    }

    private function prepareSheet(): void
    {
        $this->activeSheet->getStyle('B:B')
            ->getAlignment()
            ->setWrapText(true);
        $this->activeSheet->getStyle('C:C')
            ->getAlignment()
            ->setWrapText(true);

        $this->activeSheet->getColumnDimension('A')->setWidth(20);
        $this->activeSheet->getColumnDimension('B')->setWidth(16);
        $this->activeSheet->getColumnDimension('C')->setWidth(30);
    }

    private function makeHeader(string $company, array $data): void
    {
        $this->setTitle('A2', 'Компанія');
        $this->activeSheet->setCellValue('B2', $company);

        $this->setTitle('A3', 'Початок періоду');
        $dateFrom = $data['dateFrom'] ?? date('Y-m-d', strtotime('-1 month'));
        $dateFrom = \DateTime::createFromFormat('Y-m-d', $dateFrom)->format('d-m-Y');
        $this->activeSheet->setCellValue('B3', $dateFrom);

        $this->setTitle('A4', 'Кінець періоду');
        $dateTo = $data['dateTo'] ?? date('Y-m-d');
        $dateTo = \DateTime::createFromFormat('Y-m-d', $dateTo)->format('d-m-Y');
        $this->activeSheet->setCellValue('B4', $dateTo);

        $this->setTitle('A5', 'Статус');
        $statusValue = $data['type'] ?? '';
        $status = empty($statusValue) ? 'Всі' : $this->translator->trans($statusValue);
        $this->activeSheet->setCellValue('B5', $status);

        $this->activeSheet->mergeCells('B2:H2');
        $this->activeSheet->mergeCells('B3:H3');
        $this->activeSheet->mergeCells('B4:H4');
        $this->activeSheet->mergeCells('B5:H5');
    }

    private function setTitle(string $coordinate, string $value): void
    {
        $this->activeSheet->getCell($coordinate)
            ->setValue($value)
            ->getStyle()
            ->getFont()
            ->setBold(true);
    }

    private function addTable(int $startRow, string $title, array $transactions): int
    {
        $this->setTitle('A'.$startRow, $title);
        $this->setTableHeaders(++$startRow);

        $tableRow = $startRow + 1;
        $amountWriteOff = 0;
        $amountRefill = 0;
        foreach ($transactions as $transaction) {
            $this->makeTableRow($transaction, $tableRow);

            $type = $transaction->getType();

            switch ($type) {
                case 'refill':
                case 'discount':
                    $amountRefill += $transaction->getAmount();
                    break;
                case 'write-off-cards':
                    $amountWriteOff += $transaction->getAmount();
            }

            ++$tableRow;
        }

        $totalRowNumber = $tableRow + 1;
        $this->setTotalRow($totalRowNumber, $this->formatNumberValue($amountRefill), $this->formatNumberValue($amountWriteOff));

        return $totalRowNumber;
    }

    private function formatNumberValue(int $value): string
    {
        return number_format((float) ($value / 100), 2, ',', '');
    }

    private function setTotalRow($totalRow, $amountRefill, $amountWriteOff): void
    {
        $this->setTitle('A'.$totalRow, 'Всього списано');
        $this->setTitle('B'.$totalRow, $amountWriteOff);

        $this->setTitle('A'.++$totalRow, 'Всього надходжень');
        $this->setTitle('B'.$totalRow, $amountRefill);
    }

    private function makeTableRow(Transaction $transaction, int $row): void
    {
        $date = $transaction->getDate();
        $this->activeSheet->setCellValue('A'.$row, $date->format('d.m.Y H:i'));
        $this->activeSheet->setCellValue('B'.$row, $this->formatNumberValue($transaction->getAmount()));
        $this->activeSheet->setCellValue('C'.$row, $this->translator->trans($transaction->getType()));
    }

    private function setTableHeaders(int $rowNumber): void
    {
        $this->setTitle("A{$rowNumber}", 'Дата/час транзакції');
        $this->setTitle("B{$rowNumber}", 'Сума, грн');
        $this->setTitle("C{$rowNumber}", 'Тип');
    }
}
