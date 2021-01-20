<?php

namespace App\Clients\Infrastructure\Document\FileGenerator;

use App\Clients\Domain\ShellInformation\ShellInformation;
use App\Clients\Infrastructure\ShellInformation\Repository\Repository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer as Writer;

final class ActCheckingFileGenerator implements XlsFileGenerator
{
    /**
     * @var ShellInformation
     */
    private $shellInfo;

    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    /**
     * @var Worksheet
     */
    private $activeSheet;

    private $template = '/templates/xls/act-check-template.xlsx';

    public function __construct(
        Repository $shellInfoRepository,
        string $rootPath
    ) {
        $templateFile = $rootPath.$this->template;

        if (false === file_exists($templateFile)) {
            throw new \Exception(sprintf('Template "%s" not found', $templateFile));
        }

        $this->shellInfo = $shellInfoRepository->get();

        $this->spreadsheet = IOFactory::load($templateFile);
        $this->activeSheet = $this->spreadsheet->getActiveSheet();
    }

    public function generate(array $data, array $variables = [])
    {
        $this->prepareTemplate($variables);

        $count = count($data);
        $this->activeSheet->insertNewRowBefore(12, $count);

        $startRow = 12;
        $endRow = $startRow + ($count - 1);

        $style = $this->activeSheet->getStyle('A11');
        $this->activeSheet->duplicateStyle($style, "A{$startRow}:B{$endRow}");

        $style = $this->activeSheet->getStyleByColumnAndRow(1, $startRow, 2, $endRow);
        $style->getFont()
            ->setBold(false);
        $style->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $numberStyle = $this->activeSheet->getStyle('D11');
        $this->activeSheet->duplicateStyle($numberStyle, "C{$startRow}:D{$endRow}");

        $this->activeSheet->fromArray($data, null, "A{$startRow}");

        $writer = new Writer\Xls($this->spreadsheet);

        $pFilename = @tempnam(File::sysGetTempDir(), 'phpxltmp');
        $writer->save($pFilename);

        return fopen($pFilename, 'rb');
    }

    private function prepareTemplate(array $variables): void
    {
        $variables = array_merge($this->variables(), $variables);

        $rows = $this->activeSheet
            ->getRowIterator();
        foreach ($rows as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $value = $cell->getValue();

                if ($value instanceof RichText) {
                    foreach ($value->getRichTextElements() as $element) {
                        $text = strtr($element->getText(), $variables);
                        $element->setText($text);
                    }
                    continue;
                }

                $cell->setValue(strtr($value, $variables));
            }
        }
    }

    private function variables(): array
    {
        return [
            '{shellCompanyName}' => $this->shellInfo->getFullName(),
            '{resultCompany}' => $this->shellInfo->getFullName(),
            '{companyName}' => '{companyName}',
            '{contractNumber}' => '{contractNumber}',
            '{contractDate}' => '{contractDate}',
            '{startDate}' => '{startDate}',
            '{endDate}' => '{endDate}',
            '{totalBalance}' => '{totalBalance}',
            '{totalBalanceSpellOut}' => '{totalBalanceSpellOut}',
            '{totalWriteOff}' => '{totalWriteOff}',
            '{totalReplenishment}' => '{totalReplenishment}',
        ];
    }
}
