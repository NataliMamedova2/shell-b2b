<?php

namespace App\Import\Application\MessageBus\MessageHandler;

use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use App\Import\Application\MessageBus\Message\ImportedFile;
use App\Import\Domain\Import\File\File;
use App\Import\Domain\Import\File\Result;
use Doctrine\Common\Persistence\ObjectManager;
use Infrastructure\Interfaces\Repository\Repository;
use League\Csv\CharsetConverter;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function League\Csv\delimiter_detect;

final class ParsingFileHandler implements MessageHandlerInterface
{
    /**
     * @var Repository
     */
    private $importedFileRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var FilesystemInterface
     */
    private $defaultFilesystem;

    /**
     * @var FileDataSaverInterface[]
     */
    private $parsedFilesHandlers;

    public function __construct(
        Repository $importedFileRepository,
        ObjectManager $objectManager,
        FilesystemInterface $defaultFilesystem,
        iterable $parsedFilesHandlers
    ) {
        $this->objectManager = $objectManager;
        $this->importedFileRepository = $importedFileRepository;
        $this->defaultFilesystem = $defaultFilesystem;
        $this->parsedFilesHandlers = $parsedFilesHandlers;

        gc_enable();
    }

    public function __invoke(ImportedFile $message)
    {
        $importedFileId = $message->getFileId();

        /** @var File $importedFile */
        $importedFile = $this->importedFileRepository->findById($importedFileId);
        $importedFile->inProgress();
        $this->objectManager->flush();

        $path = $importedFile->getDestFileMetaData()->getPath();
        if (false === $this->defaultFilesystem->has($path)) {
            $importedFile->failed(sprintf('File "%s" not found', $path));

            $this->importedFileRepository->add($importedFile);
            $this->objectManager->flush();

            return;
        }

        $handler = $this->findWriterHandler($importedFile->getFullName());

        if (null === $handler) {
            $importedFile->error(sprintf('FileDataSaver not exist for "*.%s" file', $importedFile->getFullName()));

            $this->objectManager->flush();
            gc_collect_cycles();

            return;
        }

        $fileRecourse = $this->defaultFilesystem->readStream($path);
        $csv = Reader::createFromStream($fileRecourse)
            ->setOutputBOM(Reader::BOM_UTF8)
            ->skipEmptyRecords();
        CharsetConverter::addTo($csv, 'Windows-1251', 'utf-8');

        $delimitersArray = delimiter_detect($csv, [',', '|']);
        arsort($delimitersArray);

        $csv->setDelimiter(current(array_flip($delimitersArray)));

        $limit = $handler->recordsChunkSize();
        $offset = 0;

        $totalCount = $csv->count();
        $totalResult = Result::create(new \DateTimeImmutable());

        do {
            $stmt = (new Statement())
                ->offset($offset)
                ->limit($limit);

            if (method_exists($handler, 'filterRecords')) {
                $stmt = $stmt->where([$handler, 'filterRecords']);
            }

            $records = $stmt->process($csv);
            $result = $handler->handle($records->getIterator());

            $totalResult->merge($result);

            $offset += $limit;
        } while ($offset <= $totalCount);

        $importedFile = $this->importedFileRepository->findById($importedFile->getId());

        $importedFile->done($totalResult);

        $this->importedFileRepository->add($importedFile);
        $this->objectManager->flush();

        gc_collect_cycles();
    }

    private function findWriterHandler(string $fileName): ?FileDataSaverInterface
    {
        foreach ($this->parsedFilesHandlers as $handler) {
            if (!$handler instanceof FileDataSaverInterface || false === $handler->supportedFile($fileName)) {
                continue;
            }

            return $handler;
        }

        return null;
    }
}
