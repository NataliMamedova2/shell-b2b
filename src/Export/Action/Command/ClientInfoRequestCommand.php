<?php

namespace App\Export\Action\Command;

use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\Client\Client;
use App\Export\Domain\Export\Export;
use App\Export\Domain\Export\ValueObject\File;
use App\Export\Domain\Export\ValueObject\Type;
use App\Export\Infrastructure\Service\Filename;
use Doctrine\Common\Persistence\ObjectManager;
use Infrastructure\Interfaces\Repository\Repository;
use League\Csv\CharsetConverter;
use League\Csv\Writer;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ClientInfoRequestCommand extends Command
{
    protected static $defaultName = 'request-client-info:pc';

    /**
     * @var FilesystemInterface
     */
    private $defaultFilesystem;
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
        FilesystemInterface $defaultFilesystem,
        FilesystemInterface $sftpPcFilesystem,
        Repository $clientRepository,
        Repository $exportRepository,
        ObjectManager $objectManager,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->defaultFilesystem = $defaultFilesystem;
        $this->sftpPcFilesystem = $sftpPcFilesystem;
        $this->exportRepository = $exportRepository;
        $this->clientRepository = $clientRepository;
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Start export files',
            '==================',
        ]);

        $filename = $this->getFilename();

        $this->logger->info(sprintf('Start export "%s" file.', $filename->getBasename()));

        $writer = Writer::createFromString("VERSION = 001\r\n");
        CharsetConverter::addTo($writer, 'utf-8', 'Windows-1251');

        $writer
            ->setDelimiter(',')
            ->setNewline("\r\n");

        $limit = 1000;
        $offset = 0;

        $totalCount = $this->clientRepository->count();

        if (0 === $totalCount) {
            $this->logger->info('No clients for request.');

            return 0;
        }
        do {
            /** @var Client[] $clients */
            $clients = $this->clientRepository->findMany([], [], $limit, $offset);

            foreach ($clients as $client) {
                $firmAccessory = 2;
                $advancedFormat = 1;

                $record = [
                    $firmAccessory,
                    $client->getClientPcId(),
                    $advancedFormat,
                ];
                $writer->insertOne($record);
            }

            $offset += $limit;
        } while ($offset <= $totalCount);

        $basePath = sprintf('export/%s/', date('YmdHi00'));
        $path = $basePath.$filename->getBasename();

        if (false === $this->defaultFilesystem->put($path, $writer->getContent())) {
            $this->logger->error(sprintf('Save file error: "%s".', $path));

            return 0;
        }
        $sourceFileStream = $this->defaultFilesystem->readStream($path);

        $resultExportPC = $this->sftpPcFilesystem->putStream($filename->getBasename(), $sourceFileStream);

        if (false === $resultExportPC) {
            $this->logger->error(sprintf('Export to server file error: "%s".', $path));

            return 0;
        }

        $file = new File($basePath, $filename->getFilename(), $filename->getExtension(), $sourceFileMetaData['size'] ?? 0);

        $export = new Export(IdentityId::next(), $file, Type::typePC(), new \DateTimeImmutable());

        $this->exportRepository->add($export);
        $this->objectManager->flush();

        $output->writeln([
            '================',
            'End export files',
        ]);

        return 0;
    }

    private function getFilename(): Filename
    {
        return new Filename('SIDCLi_Q', 'txt');
    }
}
