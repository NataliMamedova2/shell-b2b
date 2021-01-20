<?php

namespace App\Export\Action\Command;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Domain\ValueObject\IdentityId;
use App\Application\Infrastructure\Criteria\ExportStatusCriteria;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\StopList;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\FuelLimit\FuelLimit;
use App\Clients\Domain\Invoice\Invoice;
use App\Export\Domain\Export\Export;
use App\Export\Domain\Export\Service\Filename;
use App\Export\Domain\Export\Service\FilenameGenerator;
use App\Export\Domain\Export\ValueObject\File;
use App\Export\Domain\Export\ValueObject\Type;
use Doctrine\Common\Persistence\ObjectManager;
use Infrastructure\Interfaces\Repository\Repository;
use League\Csv\CannotInsertRecord;
use League\Csv\CharsetConverter;
use League\Csv\Exception;
use League\Csv\Writer;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExportCommand extends Command
{
    protected static $defaultName = 'export';

    /**
     * @var FilenameGenerator
     */
    private $filename1CGenerator;
    /**
     * @var FilesystemInterface
     */
    private $defaultFilesystem;
    /**
     * @var FilesystemInterface
     */
    private $export1SFilesystem;
    /**
     * @var FilesystemInterface
     */
    private $sftpPcFilesystem;
    /**
     * @var Repository
     */
    private $exportRepository;
    /**
     * @var Repository
     */
    private $cardRepository;
    /**
     * @var Repository
     */
    private $cardLimitRepository;
    /**
     * @var Repository
     */
    private $invoiceRepository;
    /**
     * @var Repository
     */
    private $cardStopListRepository;
    /**
     * @var Repository
     */
    private $clientRepository;
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        FilenameGenerator $filename1CGenerator,
        Repository $exportRepository,
        FilesystemInterface $defaultFilesystem,
        FilesystemInterface $export1SFilesystem,
        FilesystemInterface $sftpPcFilesystem,
        Repository $clientRepository,
        Repository $cardRepository,
        Repository $cardLimitRepository,
        Repository $invoiceRepository,
        Repository $cardStopListRepository,
        ObjectManager $objectManager,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->filename1CGenerator = $filename1CGenerator;
        $this->defaultFilesystem = $defaultFilesystem;
        $this->export1SFilesystem = $export1SFilesystem;
        $this->sftpPcFilesystem = $sftpPcFilesystem;
        $this->exportRepository = $exportRepository;
        $this->cardRepository = $cardRepository;
        $this->cardLimitRepository = $cardLimitRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->cardStopListRepository = $cardStopListRepository;
        $this->clientRepository = $clientRepository;
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Start export files',
            '==================',
            '',
        ]);

        // .cr & .fl
        $this->exportCardsFuelLimits();

        // .sl & Sstp_Exp.txt
        $this->exportStopList();

        // .mp & .tp
        $this->exportInvoices();

        $output->writeln([
            '',
            '================',
            'End export files',
        ]);

        return 0;
    }

    /**
     * @throws CannotInsertRecord
     * @throws Exception
     */
    private function exportCardsFuelLimits(): void
    {
        $crFilename = $this->filename1CGenerator->generate('cr');
        $flFilename = $this->filename1CGenerator->generate('fl');

        $this->logger->info(sprintf('Start export "%s" file.', $crFilename->getBasename()));

        if (true === $this->export1SFilesystem->has($crFilename->getBasename())) {
            $this->logger->alert(sprintf('File: "%s" exist in dest directory.', $crFilename->getBasename()));
            return;
        }
        if (true === $this->export1SFilesystem->has($flFilename->getBasename())) {
            $this->logger->alert(sprintf('File: "%s" exist in dest directory.', $flFilename->getBasename()));
            return;
        }

        /** @var Card[] $cards */
        $cards = $this->cardRepository->findMany([
            ExportStatusCriteria::class => ExportStatus::readyForExportStatus(),
        ]);

        if (empty($cards)) {
            $this->logger->info('No recodes for export.');
            return;
        }

        foreach ($cards as $card) {
            $card->getExportStatus()->inProgress();
            $this->cardRepository->add($card);

            /** @var FuelLimit[] $fuelLimits */
            $fuelLimits = $this->cardLimitRepository->findMany([
                'cardNumber_equalTo' => $card->getCardNumber(),
            ]);
            foreach ($fuelLimits as $fuelLimit) {
                $fuelLimit->getExportStatus()->inProgress();
                $this->cardLimitRepository->add($fuelLimit);
            }
        }
        $this->objectManager->flush();

        $crWriter = $this->get1CWriter();
        $flWriter = $this->get1CWriter();

        $exportedIds = [];
        foreach ($cards as $card) {
            if (false === $card->getExportStatus()->onModeration()) {
                continue;
            }

            $exportedIds[] = $card->getId();
            $record = [
                $card->getClient1CId(),
                $card->getCardNumber(),
                $card->getCarNumber(),
                $card->getDayLimit(),
                $card->getWeekLimit(),
                $card->getMonthLimit(),
                $card->getServiceSchedule(),
                $card->getTimeUseFrom()->format('H:i:s'),
                $card->getTimeUseTo()->format('H:i:s'),
                $card->getStatus(),
            ];
            $crWriter->insertOne($record);

            /** @var FuelLimit[] $fuelLimits */
            $fuelLimits = $this->cardLimitRepository->findMany([
                'cardNumber_equalTo' => $card->getCardNumber(),
            ]);
            foreach ($fuelLimits as $fuelLimit) {
                $limitRecord = [
                    $fuelLimit->getClient1CId(),
                    $fuelLimit->getCardNumber(),
                    $fuelLimit->getFuelCode(),
                    $fuelLimit->getDayLimit(),
                    $fuelLimit->getWeekLimit(),
                    $fuelLimit->getMonthLimit(),
                    $fuelLimit->getPurseActivity(),
                ];
                $flWriter->insertOne($limitRecord);
            }
        }

        $crFileExportResult = $this->saveAndExportFile($crFilename, $crWriter->getContent());
        $flFileExportResult = $this->saveAndExportFile($flFilename, $flWriter->getContent());

        /** @var Card[] $cards */
        $cards = $this->cardRepository->findMany([
            'id_in' => $exportedIds,
        ]);
        if (false === $crFileExportResult || false === $flFileExportResult) {
            $this->logger->error('Export file error. Revert.');
            foreach ($cards as $card) {
                $card->getExportStatus()->revert();
                $this->cardRepository->add($card);

                /** @var FuelLimit[] $fuelLimits */
                $fuelLimits = $this->cardLimitRepository->findMany([
                    'cardNumber_equalTo' => $card->getCardNumber(),
                ]);
                foreach ($fuelLimits as $fuelLimit) {
                    $fuelLimit->getExportStatus()->revert();
                    $this->cardLimitRepository->add($fuelLimit);
                }
            }
            $this->objectManager->flush();

            return;
        }

        foreach ($cards as $card) {
            $card->getExportStatus()->exported();
            $this->cardRepository->add($card);

            /** @var FuelLimit[] $fuelLimits */
            $fuelLimits = $this->cardLimitRepository->findMany([
                'cardNumber_equalTo' => $card->getCardNumber(),
            ]);
            foreach ($fuelLimits as $fuelLimit) {
                $fuelLimit->getExportStatus()->exported();
                $this->cardLimitRepository->add($fuelLimit);
            }
        }
        $this->objectManager->flush();

        $this->logger->info(sprintf('Export "%s" file successfully.', $crFilename->getBasename()));
        $this->logger->info(sprintf('Export "%s" file successfully.', $flFilename->getBasename()));
    }

    /**
     * @throws Exception
     */
    private function get1CWriter(): Writer
    {
        $writer = Writer::createFromString();
        CharsetConverter::addTo($writer, 'utf-8', 'Windows-1251');

        $writer
            ->setDelimiter('|')
            ->setNewline("\r\n");
        $writer->addFormatter(
            function (array $row): array {
                array_push($row, '');

                return $row;
            }
        );

        return $writer;
    }

    private function saveAndExportFile(Filename $filename, string $content): bool
    {
        $destFilePath = $filename->getBasename();
        if (true === $this->export1SFilesystem->has($destFilePath)) {
            return false;
        }

        $basePath = sprintf('export/%s/', date('YmdHi00'));
        $path = $basePath.$filename->getBasename();

        $this->defaultFilesystem->put($path, $content);
        $sourceFileMetaData = $this->defaultFilesystem->getMetadata($path);

        $sourceFileStream = $this->defaultFilesystem->readStream($path);

        $result = $this->export1SFilesystem->writeStream($destFilePath, $sourceFileStream);

        if (false === $result) {
            $this->logger->error(sprintf('Copy file "%s" error.', $destFilePath));

            return false;
        }

        $file = new File($basePath, $filename->getFilename(), $filename->getExtension(), $sourceFileMetaData['size'] ?? 0);
        $export = new Export(IdentityId::next(), $file, Type::type1C(), new \DateTimeImmutable());

        $this->exportRepository->add($export);
        $this->objectManager->flush();

        return true;
    }

    /**
     * @throws CannotInsertRecord
     * @throws Exception
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    private function exportStopList(): void
    {
        $filename = $this->filename1CGenerator->generate('sl');
        $dest1CFilePath = $filename->getBasename();

        $pcFilename = new \App\Export\Infrastructure\Service\Filename('Sstp_Exp', 'txt');

        $this->logger->info(sprintf('Start export "%s" & "%s" file.', $filename->getBasename(), $pcFilename->getBasename()));

        if (true === $this->export1SFilesystem->has($dest1CFilePath)) {
            $this->logger->alert(sprintf('File: "%s" exist in directory.', $dest1CFilePath));
            return;
        }
        if (true === $this->sftpPcFilesystem->has($pcFilename->getBasename())) {
            $this->logger->alert(sprintf('File: "%s" exist in directory.', $pcFilename->getBasename()));
            return;
        }

        /** @var StopList[] $stopList */
        $stopList = $this->cardStopListRepository->findMany([
            ExportStatusCriteria::class => ExportStatus::readyForExportStatus(),
        ]);

        if (empty($stopList)) {
            $this->logger->info('No recodes for export.');
            return;
        }

        foreach ($stopList as $item) {
            $item->getExportStatus()->inProgress();
            $this->cardStopListRepository->add($item);
        }
        $this->objectManager->flush();

        $writer = Writer::createFromString();
        CharsetConverter::addTo($writer, 'utf-8', 'Windows-1251');

        $writer
            ->setDelimiter(',')
            ->setNewline("\r\n");

        $exportedIds = [];
        foreach ($stopList as $item) {
            if (false === $item->getExportStatus()->onModeration()) {
                continue;
            }

            $exportedIds[] = $item->getId();

            /** @var Client|null $client */
            $client = $this->clientRepository->find([
                'client1CId_equalTo' => $item->getClient1CId(),
            ]);
            if (!$client instanceof Client) {
                continue;
            }

            $clientType = 2;
            $blockState = 5;
            $record = [
                $clientType,
                $this->formatNumberValue($client->getClientPcId()),
                $item->getCard()->getCardNumber(),
                $blockState,
            ];
            $writer->insertOne($record);
        }

        $basePath = $this->saveFile($filename, $writer->getContent());
        $resultExport1C = false;
        $path = $basePath.$filename->getBasename();
        $sourceFileMetaData = $this->defaultFilesystem->getMetadata($path);
        if (false === $this->export1SFilesystem->has($dest1CFilePath)) {
            $sourceFileStream = $this->defaultFilesystem->readStream($path);

            $resultExport1C = $this->export1SFilesystem->writeStream($dest1CFilePath, $sourceFileStream);

            $file = new File($basePath, $filename->getFilename(), $filename->getExtension(), $sourceFileMetaData['size'] ?? 0);
            $export = new Export(IdentityId::next(), $file, Type::type1C(), new \DateTimeImmutable());
            $this->exportRepository->add($export);
        }

        $resultExportPC = false;
        if (true === $resultExport1C && false === $this->sftpPcFilesystem->has($pcFilename->getBasename())) {
            $this->defaultFilesystem->copy($path, $basePath.$pcFilename->getBasename());
            $sourcePcFileStream = $this->defaultFilesystem->readStream($basePath.$pcFilename->getBasename());

            $resultExportPC = $this->sftpPcFilesystem->writeStream($pcFilename->getBasename(), $sourcePcFileStream);

            $file = new File($basePath, $pcFilename->getFilename(), $pcFilename->getExtension(), $sourceFileMetaData['size'] ?? 0);
            $export = new Export(IdentityId::next(), $file, Type::typePC(), new \DateTimeImmutable());
            $this->exportRepository->add($export);
        }

        /** @var StopList[] $stopList */
        $stopList = $this->cardStopListRepository->findMany([
            'id_in' => $exportedIds,
        ]);

        if (false === $resultExport1C || false === $resultExportPC) {
            $this->logger->error('Export file error. Revert.');

            $this->export1SFilesystem->delete($dest1CFilePath);
            $this->sftpPcFilesystem->delete($dest1CFilePath);

            foreach ($stopList as $item) {
                $item->getExportStatus()->revert();
                $this->cardStopListRepository->add($item);
            }
            $this->objectManager->flush();

            return;
        }

        foreach ($stopList as $item) {
            $item->getExportStatus()->exported();
            $this->cardStopListRepository->add($item);
        }
        $this->objectManager->flush();

        $this->logger->info(sprintf('Export "%s" file successfully.', $filename->getBasename()));
        $this->logger->info(sprintf('Export "%s" file successfully.', $pcFilename->getBasename()));
    }

    private function saveFile(Filename $filename, string $content): string
    {
        $basePath = sprintf('export/%s/', date('YmdHi00'));
        $path = $basePath.$filename->getBasename();

        $this->defaultFilesystem->put($path, $content);

        return $basePath;
    }

    /**
     * @throws CannotInsertRecord
     * @throws Exception
     */
    private function exportInvoices(): void
    {
        $mpFilename = $this->filename1CGenerator->generate('mp');
        $tpFilename = $this->filename1CGenerator->generate('tp');

        $destMpFilePath = $mpFilename->getBasename();
        $destTpFilePath = $tpFilename->getBasename();

        $this->logger->info(sprintf('Start export "%s" & "%s" file.', $mpFilename->getBasename(), $tpFilename->getBasename()));

        if (true === $this->export1SFilesystem->has($destMpFilePath)) {
            $this->logger->alert(sprintf('File: "%s" exist in directory.', $destMpFilePath));
            return;
        }
        if (true === $this->export1SFilesystem->has($destTpFilePath)) {
            $this->logger->alert(sprintf('File: "%s" exist in directory.', $destTpFilePath));
            return;
        }

        /** @var Invoice[] $invoices */
        $invoices = $this->invoiceRepository->findMany([
            ExportStatusCriteria::class => ExportStatus::readyForExportStatus(),
        ]);

        if (empty($invoices)) {
            $this->logger->info('No recodes for export.');
            return;
        }

        foreach ($invoices as $invoice) {
            $invoice->getExportStatus()->inProgress();
            $this->invoiceRepository->add($invoice);
        }
        $this->objectManager->flush();

        $mpWriter = $this->get1CWriter();
        $tpWriter = $this->get1CWriter();

        $exportedIds = [];
        foreach ($invoices as $invoice) {
            if (false === $invoice->getExportStatus()->onModeration()) {
                continue;
            }

            $exportedIds[] = $invoice->getId();

            $record = [
                $invoice->getInvoiceId(),
                $invoice->getNumber(),
                $invoice->getClient1CId(),
                $invoice->getDate()->getCreationDate()->format('Y-m-d'),
                $invoice->getDate()->getExpirationDate()->format('Y-m-d'),
                $invoice->getValueTax() / 10,
                (int) round($invoice->getTotalWithoutValueTax()),
                (int) round($invoice->getTotalValueTax()),
                (int) round($invoice->getTotalWithValueTax()),
            ];
            $mpWriter->insertOne($record);

            foreach ($invoice->getItems() as $item) {
                $itemRecord = [
                    $invoice->getInvoiceId(),
                    $item->getLineNumber(),
                    $item->getFuelCode(),
                    (int) round($item->getQuantity()),
                    (int) round($item->getPriceWithValueTax()),
                    (int) round($item->getPriceWithoutValueTax()),
                    (int) round($item->getSumWithValueTax()),
                    (int) round($item->getSumValueTax()),
                    (int) round($item->getSumWithoutValueTax()),
                ];
                $tpWriter->insertOne($itemRecord);
            }
        }

        $mpFileExportResult = $this->saveAndExportFile($mpFilename, $mpWriter->getContent());

        /** @var Invoice[] $invoices */
        $invoices = $this->invoiceRepository->findMany([
            'id_in' => $exportedIds,
        ]);

        if (false === $mpFileExportResult) {
            $this->logger->error('Export file error. Revert.');

            foreach ($invoices as $invoice) {
                $invoice->getExportStatus()->revert();
                $this->invoiceRepository->add($invoice);
            }
            $this->objectManager->flush();

            return;
        }

        $tpFileExportResult = $this->saveAndExportFile($tpFilename, $tpWriter->getContent());

        if (true === $mpFileExportResult && true === $tpFileExportResult) {
            foreach ($invoices as $invoice) {
                $invoice->getExportStatus()->exported();
                $this->invoiceRepository->add($invoice);
            }
            $this->objectManager->flush();

            $this->logger->info(sprintf('Export "%s" file successfully.', $mpFilename->getBasename()));
            $this->logger->info(sprintf('Export "%s" file successfully.', $tpFilename->getBasename()));
        }
    }

    private function formatNumberValue(int $value): string
    {
        if (strlen($value) < 12) {
            $value = str_repeat('0', 12 - strlen($value)).$value;
        }

        return $value;
    }
}
