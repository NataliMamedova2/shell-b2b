<?php

namespace App\Import\Action\Command;

use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Document\Document;
use App\Clients\Domain\Document\Service\UploadDocumentFileService;
use App\Clients\Domain\Document\ValueObject\Type;
use Doctrine\Common\Persistence\ObjectManager;
use ForceUTF8\Encoding;
use Infrastructure\Interfaces\Repository\Repository;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ImportDocuments extends Command
{
    protected static $defaultName = 'import:documents';

    /**
     * @var FilesystemInterface
     */
    private $importFilesystem;
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var UploadDocumentFileService
     */
    private $uploadDocumentFileService;
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        FilesystemInterface $importFilesystem,
        UploadDocumentFileService $uploadDocumentFileService,
        Repository $repository,
        ObjectManager $objectManager,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->importFilesystem = $importFilesystem;
        $this->repository = $repository;
        $this->uploadDocumentFileService = $uploadDocumentFileService;
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Import 1C documents')
            ->addOption('debug', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $errorIo = $io->getErrorStyle();

        $output->writeln([
            'Import documents',
            '================',
        ]);

        $filesList = $this->importFilesystem->listContents();
        if (0 === count($filesList)) {
            $output->writeln([
                'No documents found in source directory',
            ]);

            return 0;
        }

        $uploadedCount = 0;
        foreach ($filesList as $file) {
            if (!isset($file['type']) || !isset($file['path']) || !isset($file['filename']) || 'file' !== $file['type']) {
                $errorIo->error('Invalid File data');
                continue;
            }

            $filename = $file['basename'];

            $encodings = ['Windows-1251', 'UTF-8'];
            if ('Windows-1251' === mb_detect_encoding($filename, $encodings)) {
                $filename = iconv('Windows-1251', 'utf8', $filename);
            }
            $filename = Encoding::UTF8FixWin1252Chars($filename);

            if (false === $this->validateFilename($filename)) {
                $massage = sprintf('Invalid file name: "%s"', $filename);

                $errorIo->error($massage);
                $this->logger->error($massage);
                continue;
            }

            $fileData = $this->parseFileName($filename);

            $sourceFileStream = $this->importFilesystem->readStream($file['path']);
            $fileObject = $this->uploadDocumentFileService->upload($sourceFileStream, $fileData['name'], $file['extension']);

            $document = Document::createUploadedDocument(
                new Client1CId($fileData['client1CId']),
                $fileData['type'],
                $fileObject,
                new \DateTimeImmutable()
            );

            $this->repository->add($document);

            if (false === $input->getOption('debug')) {
                $this->importFilesystem->delete($file['path']);
            }
            ++$uploadedCount;
        }

        $this->objectManager->flush();

        $io->table(['Type', 'Count'], [
            ['Uploaded:', $uploadedCount],
        ]);

        return 0;
    }

    private function validateFilename(string $filename): bool
    {
        $explode = explode('_', $filename);
        if (count($explode) < 3) {
            return false;
        }

        $types = $this->getAvailableTypes();
        if (false === array_key_exists($explode[0], $types)) {
            return false;
        }

        return true;
    }

    private function parseFileName(string $filename)
    {
        $explode = explode('_', $filename);
        if (count($explode) < 3) {
            throw new \InvalidArgumentException('Invalid file name');
        }

        $type = $this->getDocumentTypeMapper($explode[0]);
        $name = pathinfo($explode[2], PATHINFO_FILENAME);

        return [
            'type' => $type,
            'client1CId' => $explode[1],
            'name' => $name.'_'.$type->getValue(),
        ];
    }

    private function getDocumentTypeMapper(string $type): Type
    {
        $types = $this->getAvailableTypes();

        if (!isset($types[$type])) {
            throw new \InvalidArgumentException('Unsupported file');
        }

        return $types[$type];
    }

    private function getAvailableTypes(): array
    {
        return [
            'АС' => Type::actChecking(),
            'ЛП' => Type::appendixPetroleumProducts(),
            'ОК' => Type::cardInvoice(),
            'АФ' => Type::acceptanceTransferAct(),
        ];
    }
}
