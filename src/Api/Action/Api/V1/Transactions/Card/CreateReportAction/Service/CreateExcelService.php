<?php

namespace App\Api\Action\Api\V1\Transactions\Card\CreateReportAction\Service;

use App\Clients\Domain\Fuel\Type\Type as FuelType;
use App\Clients\Domain\Transaction\Card\Transaction;
use PhpOffice\PhpSpreadsheet\Settings as Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer as Writer;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CreateExcelService implements TransactionsXlsWriter
{
    private const LIMIT = 1250;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Worksheet
     */
    private $activeSheet;

    /**
     * @var Spreadsheet
     */
    private $spreadSheet;

    private $fuelTypes = [];

    private $cardNumbers = [];

    private $fuelNames = [];

    private $companies = [];

    private $cache;

    public function __construct(TranslatorInterface $translator, AdapterInterface $cache)
    {
        $this->cache = $cache;
        $psr16Cache = new Psr16Cache($this->cache);
        Settings::setCache($psr16Cache);
        Settings::setLibXmlDisableEntityLoader(true);
        $this->translator = $translator;
        $this->spreadSheet = new Spreadsheet();
        $this->activeSheet = $this->spreadSheet->getActiveSheet();
    }

    public function create(
        array $transactions,
        array $replenishmentTransactions,
        array $fuelTypes,
        string $company,
        array $filterData = [],
        array $companies = []
    ): Writer\Xls {
        $this->fuelTypes = $fuelTypes;

        $cardNumber = $filterData['cardNumber'] ?? '';
        $this->companies = $companies;

        $this->prepareSheet();
        $this->makeHeader($company, $filterData);

        $lastRow = $this->addTable(9, 'Транзакції "Списання/Повернення"', $transactions);

        $replenishmentTableStartRow = $lastRow + 3;
        $this->addTable($replenishmentTableStartRow, 'Транзакції "Поповнення"', $replenishmentTransactions);

        $filterCardsNumbers = (!empty($cardNumber)) ? $this->cardNumbers : [];
        $this->setFilterSelectedValue(6, $filterCardsNumbers);

        return new Writer\Xls($this->spreadSheet);
    }

    private function prepareSheet(): void
    {
        $this->activeSheet->getStyle('B:B')
            ->getAlignment()
            ->setWrapText(true);
        $this->activeSheet->getStyle('G:G')
            ->getAlignment()
            ->setWrapText(true);

        $this->activeSheet->getColumnDimension('A')->setWidth(20);
        $this->activeSheet->getColumnDimension('B')->setWidth(16);
        $this->activeSheet->getColumnDimension('C')->setWidth(30);
        $this->activeSheet->getColumnDimension('D')->setWidth(11);
        $this->activeSheet->getColumnDimension('F')->setWidth(11);
        $this->activeSheet->getColumnDimension('G')->setWidth(55);
        $this->activeSheet->getColumnDimension('H')->setWidth(12);
    }

    private function makeHeader(string $company, array $data): void
    {
        $this->setTitle('A2', 'Компанія');
        $this->activeSheet->setCellValue('B2', $company);

        $this->setTitle('A3', 'Початок періоду');
        $dateFrom = $data['dateFrom'] ?? date('d-m-Y');
        $this->activeSheet->setCellValue('B3', $dateFrom);

        $this->setTitle('A4', 'Кінець періоду');
        $dateTo = $data['dateTo'] ?? date('d-m-Y');
        $this->activeSheet->setCellValue('B4', $dateTo);

        $this->setTitle('A5', 'ТМЦ');

        $suppliesType = '';
        if (isset($data['supplies']) && false === empty($data['supplies'])) {
            $fuelCode = [];
            foreach ($data['supplies'] as $item) {
                $fuelCode[] = $this->getFuelName($item);
            }
            $suppliesType = \implode(',', $fuelCode);
        }
        $supply = empty($suppliesType) ? 'Всі' : $suppliesType;
        $this->activeSheet->setCellValue('B5', $supply);

        $this->setTitle('A6', 'Номер картки');
        $this->activeSheet->setCellValue('B6', 'Всі');

        $this->setTitle('A7', 'Статус');
        $statusValue = $data['type'] ?? $data['status'] ?? '';
        $status = empty($statusValue) ? 'Всі' : $this->translator->trans($statusValue);
        $this->activeSheet->setCellValue('B7', $status);

        $this->activeSheet->mergeCells('B2:H2');
        $this->activeSheet->mergeCells('B3:H3');
        $this->activeSheet->mergeCells('B4:H4');
        $this->activeSheet->mergeCells('B5:H5');
        $this->activeSheet->mergeCells('B6:H6');
        $this->activeSheet->mergeCells('B7:H7');
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
        $volumeSum = 0;
        $amountSum = 0;

        if (count($transactions) > self::LIMIT) {
            $transactions = \array_slice($transactions, 0, self::LIMIT);
        }

        foreach ($transactions as $transaction) {
            $fuelName = $this->getFuelName($transaction->getFuelCode());

            $this->cardNumbers[$transaction->getCardNumber()] = $transaction->getCardNumber();
            $this->fuelNames[$transaction->getFuelCode()] = $fuelName;

            $this->makeTableRow($transaction, $tableRow, $fuelName);

            $volumeVal = $transaction->getFuelQuantity();
            $volumeSum += (true === $transaction->isReturn()) ? -$volumeVal : $volumeVal;

            $amountVal = $transaction->getDebit();
            $amountSum += (true === $transaction->isReturn()) ? -$amountVal : $amountVal;

            ++$tableRow;
        }

        $totalRowNumber = $tableRow + 1;
        $this->setTotalRow($totalRowNumber, $this->formatNumberValue($volumeSum), $this->formatNumberValue($amountSum));

        return $totalRowNumber;
    }

    private function setTableHeaders(int $rowNumber): void
    {
        $this->setTitle("A{$rowNumber}", 'Дата/час транзакції');
        $this->setTitle("B{$rowNumber}", 'Номер карти');
        $this->setTitle("C{$rowNumber}", 'ТМЦ');
        $this->setTitle("D{$rowNumber}", 'К-сть, л');
        $this->setTitle("E{$rowNumber}", 'Ціна, грн');
        $this->setTitle("F{$rowNumber}", 'Сума, грн');
        $this->setTitle("G{$rowNumber}", 'АЗС');
        $this->setTitle("H{$rowNumber}", 'Тип');
        $this->setTitle("I{$rowNumber}", 'Компанія');
        $this->activeSheet->mergeCells("I{$rowNumber}:R{$rowNumber}");
    }

    private function getFuelName(string $fuelCode): string
    {
        $fuelName = '';
        if (isset($this->fuelTypes[$fuelCode]) && $this->fuelTypes[$fuelCode] instanceof FuelType) {
            $fuelName = $this->fuelTypes[$fuelCode]->getFuelName();
        }

        return $fuelName;
    }

    private function makeTableRow(Transaction $cardTransaction, int $row, $fuelName): void
    {
        $date = $cardTransaction->getPostDate();
        $this->activeSheet->setCellValue('A'.$row, $date->format('d.m.Y H:i'));
        $this->activeSheet->setCellValue('B'.$row, $cardTransaction->getCardNumber());
        $this->activeSheet->setCellValue('C'.$row, $fuelName);

        $this->activeSheet
            ->setCellValue('D'.$row, $this->formatNumberValue($cardTransaction->getFuelQuantity()));
        $this->textAlignment('D'.$row);

        $this->activeSheet
            ->setCellValue('E'.$row, $this->formatNumberValue($cardTransaction->getPrice()));
        $this->textAlignment('E'.$row);

        $this->activeSheet
            ->setCellValue('F'.$row, $this->formatNumberValue($cardTransaction->getDebit()));
        $this->textAlignment('F'.$row);

        $this->activeSheet->setCellValue('G'.$row, $cardTransaction->getAzsName());

        $type = $cardTransaction->getTypeName();
        $this->activeSheet->setCellValue('H'.$row, $this->translator->trans($type));
        $companyName = $this->companies[$cardTransaction->getClient1CId()] ?? '';
        $this->activeSheet->setCellValue('I'.$row, $companyName);
        $this->activeSheet->mergeCells("I{$row}:R{$row}");
    }

    private function formatNumberValue(int $value): string
    {
        return number_format((float) ($value / 100), 2, ',', '');
    }

    private function setTotalRow($totalRow, $volumeSum, $amountSum): void
    {
        $this->setTitle('A'.$totalRow, 'Всього');
        $this->setTitle('D'.$totalRow, $volumeSum);
        $this->textAlignment('D'.$totalRow);

        $this->setTitle('F'.$totalRow, $amountSum);
        $this->textAlignment('F'.$totalRow);
    }

    private function textAlignment($coordinate, $value = Alignment::HORIZONTAL_RIGHT): void
    {
        $this->activeSheet->getStyle($coordinate)
            ->getAlignment()
            ->setHorizontal($value);
    }

    private function setFilterSelectedValue(int $coordinate, array $valuesList): void
    {
        $valuesListString = empty($valuesList) ? 'Всі' : implode(', ', $valuesList);
        $this->activeSheet->setCellValue('B'.$coordinate, $valuesListString);
    }
}
