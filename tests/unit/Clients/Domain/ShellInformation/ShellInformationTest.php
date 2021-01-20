<?php

namespace Tests\Unit\Clients\Domain\ShellInformation;

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
use PHPUnit\Framework\TestCase;

final class ShellInformationTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ShellInformationId::fromString($string);

        $fullName = new FullName('Товариство з обмеженою відповідальністю "Альянс Холдинг1212"');
        $zkpo = new Zkpo('11130111');
        $ipn = new Ipn('111301116111');
        $certificateNumber = new CertificateNumber('111330111');
        $telephoneNumber = new TelephoneNumber('0994950999');
        $postAddress = new PostAddress('м. Київ');
        $currentAccount = new CurrentAccount('11112223334');
        $currentMfo = new CurrentMfo('000000');
        $currentBank = new CurrentBank('Банк');
        $email = new Email('');
        $site = new Site('');
        $nds = new Nds('2000');
        $invoiceValidUntilConst = new InvoiceValidUntilConst('3');
        $invoicePrenameConst = new InvoicePrenameConst('WWW');

        $dateTime = new \DateTimeImmutable();

        $entity = ShellInformation::create(
            $identity,
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

        $this->assertEquals($fullName->getValue(), (string) $entity->getFullName());
        $this->assertEquals($invoiceValidUntilConst->getValue(), (string) $entity->getInvoiceValidDays());
        $this->assertEquals($invoicePrenameConst->getValue(), (string) $entity->getInvoiceNumberPrefix());
        $this->assertEquals($nds->getValue(), (string) $entity->getValueAddedTax());
    }

    public static function createValidEntity(): ShellInformation
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ShellInformationId::fromString($string);

        $fullName = new FullName('Товариство з обмеженою відповідальністю "Альянс Холдинг1212"');
        $zkpo = new Zkpo('11130111');
        $ipn = new Ipn('111301116111');
        $certificateNumber = new CertificateNumber('111330111');
        $telephoneNumber = new TelephoneNumber('0994950999');
        $postAddress = new PostAddress('м. Київ');
        $currentAccount = new CurrentAccount('11112223334');
        $currentMfo = new CurrentMfo('000000');
        $currentBank = new CurrentBank('Банк');
        $email = new Email('');
        $site = new Site('');
        $nds = new Nds('2000');
        $invoiceValidUntilConst = new InvoiceValidUntilConst('3');
        $invoicePrenameConst = new InvoicePrenameConst('WWW');

        $dateTime = new \DateTimeImmutable();

        return ShellInformation::create(
            $identity,
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
    }
}
