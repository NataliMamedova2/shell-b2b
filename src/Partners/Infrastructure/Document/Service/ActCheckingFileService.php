<?php

namespace App\Partners\Infrastructure\Document\Service;

use App\Clients\Domain\Document\ValueObject\File;
use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use App\Clients\Infrastructure\Discount\Repository\DiscountRepository;
use App\Clients\Infrastructure\Document\FileGenerator\XlsFileGenerator;
use App\Partners\Domain\Partner\Partner;
use App\Partners\Domain\Transaction\Transaction;
use App\Partners\Infrastructure\Repository\Transaction\TransactionRepository;
use App\TwigBundle\Service\SpellOut;
use DateTimeInterface;
use FilesUploader\File\PathGeneratorInterface;
use League\Flysystem\FilesystemInterface;

final class ActCheckingFileService
{
    /**
     * @var XlsFileGenerator
     */
    private $fileGenerator;
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;
    /**
     * @var DiscountRepository
     */
    private $discountRepository;

    /**
     * @var SpellOut
     */
    private $spellOutService;
    /**
     * @var PathGeneratorInterface
     */
    private $pathGenerator;
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    private $totalWriteOff = 0;

    private $totalReplenishment = 0;

    public function __construct(
        XlsFileGenerator $fileGenerator,
        TransactionRepository $transactionRepository,
        DiscountRepository $discountRepository,
        SpellOut $spellOutService,
        PathGeneratorInterface $pathGenerator,
        FilesystemInterface $filesystem
    ) {
        $this->fileGenerator = $fileGenerator;
        $this->transactionRepository = $transactionRepository;
        $this->discountRepository = $discountRepository;
        $this->spellOutService = $spellOutService;
        $this->pathGenerator = $pathGenerator;
        $this->filesystem = $filesystem;
    }

    public function create(Partner $partner, DateTimeInterface $startDate, DateTimeInterface $endDate): File
    {
        $data = $this->getTableData($partner, $startDate, $endDate);

        $balance = $partner->getBalance();
        $contractDate = null;
        if (null !== $partner->getContractDate()) {
            $contractDate = $partner->getContractDate()->format('Y-m-d');
        }
        $variables = [
            '{balance}' => $this->formatNumberValue($balance),
            '{companyName}' => $partner->getTitle(),
            '{contractNumber}' => $partner->getContractNumber(),
            '{contractDate}' => $contractDate,
            '{startDate}' => $startDate->format('d.m.Y'),
            '{endDate}' => $endDate->format('d.m.Y'),
            '{totalWriteOff}' => $this->formatNumberValue($this->totalWriteOff),
            '{totalReplenishment}' => $this->formatNumberValue($this->totalReplenishment),
        ];

        $totalBalance = $this->formatNumberValue($balance + $this->totalReplenishment - $this->totalWriteOff);
        $variables['{totalBalance}'] = $totalBalance;
        $variables['{totalBalanceSpellOut}'] = $this->spellOutService->spellOut($totalBalance);

        if ($totalBalance >= 0) {
            $variables['{resultCompany}'] = $partner->getTitle();
        }

        $name = $this->generateFilename();
        $path = $this->pathGenerator->generate($name, ['pathPrefix' => 'documents']);

        $ext = 'xls';
        $file = new File($path, $name, $ext);

        $writer = $this->fileGenerator->generate($data, $variables);

        $this->filesystem->putStream($file->getFile(), $writer);

        return $file;
    }

    private function getTableData(Partner $partner, DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        /** @var Transaction[] $transactionsReplenishment */
        $transactionsReplenishment = $this->transactionRepository->findMany([
            'client1CId_equalTo' => $partner->getClient1CId(),
            'postDate_greaterThanOrEqualTo' => $startDate,
            'postDate_lessThanOrEqualTo' => $endDate,
            'write_off_type_equalTo' => Type::replenishment()->getValue(),
        ], ['postDate' => 'ASC']);

        $data = [];
        foreach ($transactionsReplenishment as $transaction) {
            $data[] = $this->addReturnTableData($transaction->getPostDate(), $transaction->getDebit());

            $this->totalReplenishment += $transaction->getDebit();
        }

        /** @var array $transactionsWriteOff */
        $transactionsWriteOff = $this->transactionRepository
            ->calculatePartnerDebitSumByMonths(
                $partner,
                Type::writeOff(),
                $startDate,
                $endDate
            );

        $discounts = $this->discountRepository
            ->calculateClientDebitSumByMonths(
                $partner->getClient1CId(),
                $startDate,
                $endDate
            );

        foreach ($transactionsWriteOff as $transaction) {
            $discount = $discounts[$transaction['date']] ?? 0;
            $writeOffSum = $transaction['sum'] - $discount;

            $data[] = $this->addWriteOffTableData(new \DateTimeImmutable($transaction['date']), $writeOffSum);

            $this->totalWriteOff += $writeOffSum;
        }

        return $data;
    }

    private function addReturnTableData(DateTimeInterface $date, $amount): array
    {
        return [
            sprintf('Поповнення %s', $date->format('d.m.Y')),
            '',
            '',
            $this->formatNumberValue($amount),
        ];
    }

    private function addWriteOffTableData(DateTimeInterface $date, $amount): array
    {
        $stringDate = $date->format('t.m.Y');
        $today = new \DateTimeImmutable();
        if ($today->format('n') === $date->format('n') &&
            $today->format('j') < $today->format('t')
        ) {
            $stringDate = $today->format('d.m.Y');
        }

        return [
            sprintf('Списання по карткам %s', $stringDate),
            '',
            $this->formatNumberValue($amount),
        ];
    }

    private function formatNumberValue($value)
    {
        if (empty($value)) {
            return 0;
        }

        return $value / 100;
    }

    private function generateFilename()
    {
        return sprintf('%s_%d', sha1(time()), time());
    }
}
