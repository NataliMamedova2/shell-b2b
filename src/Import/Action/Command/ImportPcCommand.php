<?php

namespace App\Import\Action\Command;

use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use App\Import\Application\MessageBus\Message\ImportedFile;
use App\Import\Domain\Import\File\File;
use App\Import\Domain\Import\File\ValueObject\FileId;
use App\Import\Domain\Import\File\ValueObject\MetaData;
use App\Import\Domain\Import\File\ValueObject\Status\DoneStatus;
use App\Import\Domain\Import\File\ValueObject\Status\FailedStatus;
use App\Import\Domain\Import\File\ValueObject\Status\Status;
use App\Import\Domain\Import\Import;
use Doctrine\Common\Persistence\ObjectManager;
use Infrastructure\Interfaces\Repository\Repository;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ImportPcCommand extends Command
{
    protected static $defaultName = 'import:pc';

    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var FilesystemInterface
     */
    private $sourceFilesystem;
    /**
     * @var FilesystemInterface
     */
    private $defaultFilesystem;
    /**
     * @var Repository
     */
    private $importRepository;
    /**
     * @var Repository
     */
    private $fileRepository;
    /**
     * @var MessageBusInterface
     */
    private $bus;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var iterable
     */
    private $parsedFilesHandlers;

    public function __construct(
        ObjectManager $objectManager,
        FilesystemInterface $sourceFilesystem,
        FilesystemInterface $defaultFilesystem,
        Repository $importRepository,
        Repository $fileRepository,
        MessageBusInterface $bus,
        LoggerInterface $logger,
        iterable $parsedFilesHandlers
    ) {
        parent::__construct();
        $this->objectManager = $objectManager;
        $this->sourceFilesystem = $sourceFilesystem;
        $this->defaultFilesystem = $defaultFilesystem;
        $this->importRepository = $importRepository;
        $this->fileRepository = $fileRepository;
        $this->bus = $bus;
        $this->logger = $logger;
        $this->parsedFilesHandlers = $parsedFilesHandlers;
    }

    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Import files data from PC')
            ->addArgument(
                'status',
                InputArgument::IS_ARRAY,
                sprintf('Statuses for re-import. Available: "%s"', implode(' ', Status::getNames())),
                ['error']
            )
            ->addOption('debug', null, InputOption::VALUE_NONE, 'If we should remove imported file or not');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Start import files from PC',
            '==================',
            '',
        ]);

        $reImportStatuses = $input->getArgument('status');

        /** @var File[] $files */
        $files = $this->fileRepository->findMany([
            'status_notIn' => [
                (new DoneStatus())->getValue(),
                (new FailedStatus())->getValue(),
            ],
        ], ['fileName' => 'ASC']);

        $ignoreExt = [];
        foreach ($files as $file) {
            $ignoreExt[] = $file->getExtension();

            if (true === in_array($file->getStatus(), $reImportStatuses)) {
                $output->writeln([
                    sprintf('Add file "%s" to RE-Import queue', $file->getDestFileMetaData()->getPath()),
                ]);
                $this->addFileToParsingQueue($file);
            }
        }
        $ignoreExt = array_unique($ignoreExt);

        $import = Import::start(new \DateTimeImmutable());

        $filesList = $this->sourceFilesystem->listContents();
        if (0 === count($filesList)) {
            $import->done(new \DateTimeImmutable());

            $output->writeln('No files found in source directory');

            $this->importRepository->add($import);
            $this->objectManager->flush();

            return 0;
        }

        $import->processing();
        $destinationFolderName = sprintf('import/%s/PC/', date('YmdHi00'));
        foreach ($filesList as $file) {
            if ('file' !== $file['type']) {
                continue;
            }
            if (false === $input->getOption('debug') && true === in_array($file['extension'], $ignoreExt)) {
                $this->logger->alert(sprintf('Previous file "*.%s" was not imported', $file['extension']));
                continue;
            }
            if (false === $this->fileNameValidation($file['basename'])) {
                $output->writeln([
                    sprintf('File: "%s" not supported', $file['basename']),
                ]);
                continue;
            }
            $sourceFilePath = $file['path'];
            if (false === $this->sourceFilesystem->has($sourceFilePath)) {
                $output->writeln([
                    sprintf('File: "%s" not found', $sourceFilePath),
                ]);
                continue;
            }

            $sourceFileStream = $this->sourceFilesystem->readStream($sourceFilePath);
            if (false === $sourceFileStream) {
                $output->writeln([
                    sprintf('Can\'t read file: "%s"', $sourceFilePath),
                ]);
                continue;
            }
            $sourceFileMetaData = $this->sourceFilesystem->getMetadata($sourceFilePath);

            $importFile = File::create(
                $import,
                $file['filename'],
                $file['extension'],
                $file['size'],
                MetaData::fromArray($sourceFileMetaData),
                new \DateTimeImmutable()
            );
            $import->addFile($importFile);

            $destFilePath = $destinationFolderName.$file['basename'];

            $result = $this->defaultFilesystem->putStream($destFilePath, $sourceFileStream);

            if (false === $result) {
                $this->logger->error(sprintf('Copy file error: from "%s" to "%s"', $destFilePath, $sourceFilePath));
                $importFile->failed();
                continue;
            }
            $destFileMetaData = $this->defaultFilesystem->getMetadata($destFilePath);

            $importFile->setDestFileMetaData(MetaData::fromArray($destFileMetaData));

            if (false === $input->getOption('debug')) {
                $this->sourceFilesystem->delete($file['path']);
            }
        }

        if (0 === count($import->getFiles())) {
            $import->done(new \DateTimeImmutable());
        }

        $this->importRepository->add($import);
        $this->objectManager->flush();

        foreach ($import->getFiles() as $file) {
            $output->writeln([
                sprintf('Add file "%s" to parsing queue', $file->getFullName()),
            ]);

            $this->addFileToParsingQueue($file);
        }

        $output->writeln([
            '',
            '================',
            'End import files',
        ]);

        return 0;
    }

    private function addFileToParsingQueue(File $file): void
    {
        $fileId = FileId::fromString($file->getId());

        $envelope = new Envelope(new ImportedFile($fileId));
        $this->bus->dispatch($envelope);
    }

    private function fileNameValidation(string $fileName): bool
    {
        $isSupported = false;
        foreach ($this->parsedFilesHandlers as $handler) {
            if ($handler instanceof FileDataSaverInterface && true === $handler->supportedFile($fileName)) {
                $isSupported = true;
                continue;
            }
        }

        return $isSupported;
    }
}
