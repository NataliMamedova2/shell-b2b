<?php

namespace App\Import\Application\FileDataSaver;

use App\Clients\Domain\ShellInformation\ShellInformation;
use App\Clients\Domain\ShellInformation\ValueObject\CertificateNumber;
use App\Clients\Domain\ShellInformation\ValueObject\CurrentAccount;
use App\Clients\Domain\ShellInformation\ValueObject\CurrentBank;
use App\Clients\Domain\ShellInformation\ValueObject\CurrentMfo;
use App\Clients\Domain\ShellInformation\ValueObject\Email;
use App\Clients\Domain\ShellInformation\ValueObject\FullName;
use App\Clients\Domain\ShellInformation\ValueObject\InvoicePrenameConst;
use App\Clients\Domain\ShellInformation\ValueObject\InvoiceValidUntilConst;
use App\Clients\Domain\ShellInformation\ValueObject\Ipn;
use App\Clients\Domain\ShellInformation\ValueObject\Nds;
use App\Clients\Domain\ShellInformation\ValueObject\PostAddress;
use App\Clients\Domain\ShellInformation\ValueObject\ShellInformationId;
use App\Clients\Domain\ShellInformation\ValueObject\Site;
use App\Clients\Domain\ShellInformation\ValueObject\TelephoneNumber;
use App\Clients\Domain\ShellInformation\ValueObject\Zkpo;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Import\Application\FileDataSaver\Writer\InsertUpdateWriter;

final class ShellInformationDataSaver extends InsertUpdateWriter implements FileDataSaverInterface
{
    private const FILE_EXTENSION = 'si';

    public function supportedFile(string $fileName): bool
    {
        return self::FILE_EXTENSION === pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public function getUniqueKeyFromEntity($entity): ?string
    {
        if (!$entity instanceof ShellInformation) {
            return null;
        }

        return 'unique';
    }

    public function buildSelectQuery(EntityManagerInterface $entityManager, \ArrayIterator $items): ?AbstractQuery
    {
        $query = $entityManager->createQuery(
            sprintf('SELECT c FROM %s c', ShellInformation::class)
        );
        $query->setMaxResults(1);

        return $query;
    }

    public function getUniqueKeyFromRecord(array $record): string
    {
        return 'unique';
    }

    public function createEntity(array $record): object
    {
        $fullName = new FullName($record[0]);
        $zkpo = new Zkpo($record[1]);
        $ipn = new Ipn($record[2]);
        $certificateNumber = new CertificateNumber($record[3]);
        $telephoneNumber = new TelephoneNumber($record[4]);
        $postAddress = new PostAddress($record[5]);
        $currentAccount = new CurrentAccount($record[6]);
        $currentMfo = new CurrentMfo($record[7]);
        $currentBank = new CurrentBank($record[8]);
        $email = new Email($record[9]);
        $site = new Site($record[10]);
        $nds = new Nds($record[11]);
        $invoiceValidUntilConst = new InvoiceValidUntilConst($record[12]);
        $invoicePrenameConst = new InvoicePrenameConst($record[13]);

        $dateTime = new \DateTimeImmutable();

        $entity = ShellInformation::create(
            ShellInformationId::next(),
            $fullName,
            $zkpo,
            $ipn,
            $certificateNumber,
            $telephoneNumber,
            $postAddress,
            $currentAccount,
            $currentMfo,
            $currentBank,
            $email,
            $site,
            $nds,
            $invoiceValidUntilConst,
            $invoicePrenameConst,
            $dateTime
        );

        return $entity;
    }

    public function updateEntity($entity, array $record): void
    {
        $fullName = new FullName($record[0]);
        $zkpo = new Zkpo($record[1]);
        $ipn = new Ipn($record[2]);
        $certificateNumber = new CertificateNumber($record[3]);
        $telephoneNumber = new TelephoneNumber($record[4]);
        $postAddress = new PostAddress($record[5]);
        $currentAccount = new CurrentAccount($record[6]);
        $currentMfo = new CurrentMfo($record[7]);
        $currentBank = new CurrentBank($record[8]);
        $email = new Email($record[9]);
        $site = new Site($record[10]);
        $nds = new Nds($record[11]);
        $invoiceValidUntilConst = new InvoiceValidUntilConst($record[12]);
        $invoicePrenameConst = new InvoicePrenameConst($record[13]);

        $entity->update(
            $fullName,
            $zkpo,
            $ipn,
            $certificateNumber,
            $telephoneNumber,
            $postAddress,
            $currentAccount,
            $currentMfo,
            $currentBank,
            $email,
            $site,
            $nds,
            $invoiceValidUntilConst,
            $invoicePrenameConst
        );
    }
}
