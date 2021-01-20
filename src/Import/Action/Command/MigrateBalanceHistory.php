<?php

namespace App\Import\Action\Command;

use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\ClientInfo\BalanceHistory;
use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Domain\ClientInfo\ValueObject\ClientPcId;
use App\Import\Domain\Import\File\File;
use App\Import\Domain\Import\File\ValueObject\Status\DoneStatus;
use Doctrine\ORM\EntityManager;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Infrastructure\Interfaces\Repository\Repository;
use League\Csv\CharsetConverter;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigrateBalanceHistory extends Command
{
    protected static $defaultName = 'migrate:balance-history';
    /**
     * @var FilesystemInterface
     */
    private $filesystem;
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Repository
     */
    private $clientInfoRepository;

    public function __construct(
        FilesystemInterface $filesystem,
        Repository $clientInfoRepository,
        Repository $repository,
        EntityManager $entityManager,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->clientInfoRepository = $clientInfoRepository;
    }

    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Migrate/Update clients balance history from imported "PIDCLi_R" files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $output->writeln([
            'Migrate client balance history',
            '==============================',
        ]);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('All clients balance histories will be overwritten. Continue?(y/N)', false);

        if (!$helper->ask($input, $output, $question)) {
            return 0;
        }

        $this->disableDoctrineLogging();

        $className = File::class;
        $query = $this->entityManager->createQuery(
            "SELECT fl
                FROM {$className} fl
            WHERE fl.fileName = :filename
                AND fl.status = :status
                AND to_char(fl.createdAt, 'YYYY-mm-dd') = to_char(date_trunc('month', fl.createdAt), 'YYYY-mm-dd')
            ORDER BY fl.createdAt ASC"
        );

        $query->setParameters([
            ':filename' => 'PIDCLi_R',
            ':status' => (new DoneStatus())->getValue(),
        ]);

        $batchSize = 500;
        $iterable = SimpleBatchIteratorAggregate::fromQuery($query, $batchSize);

        $migratedDate = [];
        $processedFilesCount = 0;
        foreach ($iterable as $row) {
            /** @var File $fileEntity */
            $fileEntity = $row[0];

            $date = $fileEntity->getCreatedAt()->format('Y-m-d');

            if (isset($migratedDate[$date])) {
                continue;
            }

            $fileInfo = $fileEntity->getDestFileMetaData();
            $path = $fileInfo->getPath();
            if (false === $this->filesystem->has($path)) {
                $this->logger->alert(sprintf('File %s not found', $path));
                continue;
            }

            $fileContent = $this->filesystem->readStream($path);
            if (false === is_resource($fileContent)) {
                $this->logger->error(sprintf('Can\'t read file: %s', $path));
                continue;
            }
            ++$processedFilesCount;

            $csv = Reader::createFromStream($fileContent)
                ->setOutputBOM(Reader::BOM_UTF8)
                ->skipEmptyRecords()
                ->setDelimiter(',');
            CharsetConverter::addTo($csv, 'Windows-1251', 'utf-8');

            $limit = 1000;
            $offset = 0;

            $totalCount = $csv->count();
            do {
                $stmt = (new Statement())
                    ->where(function ($record) {
                        return '&' === $record[0] && !empty(\intval($record[2]));
                    })
                    ->offset($offset)
                    ->limit($limit);

                $records = $stmt->process($csv);

                foreach ($records->getRecords() as $record) {
                    $clientPcId = new ClientPcId($record[2]);
                    $clientInfo = $this->clientInfoRepository->find(['clientPcId_equalTo' => $clientPcId->getValue()]);

                    if (!$clientInfo instanceof ClientInfo) {
                        continue;
                    }

                    $history = $this->repository->find([
                        'clientPc.clientPcId_equalTo' => $clientPcId->getValue(),
                        'date_equalTo' => $fileEntity->getCreatedAt()->format('Y-m-d')
                    ]);

                    if (!$history instanceof BalanceHistory) {
                        $history = new BalanceHistory(IdentityId::next(), $clientInfo, $fileEntity->getCreatedAt());
                    }

                    $this->entityManager->persist($history);
                }

                $offset += $limit;
            } while ($offset <= $totalCount);
            $this->entityManager->flush();

            $migratedDate[$date] = $date;
        }

        $io->table([], [
            ['Processed files:', $processedFilesCount],
            ['Migrated Dates:', implode(' | ', $migratedDate)],
        ]);

        $output->writeln([
            '==============================',
            'Done Migrate client balance history',
        ]);

        return 0;
    }

    private function disableDoctrineLogging(): void
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $config->setSQLLogger(null);
    }
}
