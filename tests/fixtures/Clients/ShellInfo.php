<?php

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
use League\FactoryMuffin\FactoryMuffin;

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(ShellInformation::class)
    ->setMaker(function ($class) {
        /* @var ShellInformation $class */
        return $class::create(
            ShellInformationId::next(),
            new FullName('Товариство з обмеженою відповідальністю "Roga & Kopita"'),
            new Zkpo('11130111'),
            new Ipn('111301116111'),
            new CertificateNumber('111330111'),
            new TelephoneNumber('0994950999'),
            new PostAddress('м. Київ'),
            new CurrentAccount('11112223334'),
            new CurrentMfo('000000'),
            new CurrentBank('Банк'),
            new Email(''),
            new Site(''),
            new Nds('2000'),
            new InvoiceValidUntilConst('3'),
            new InvoicePrenameConst('WWW'),
            new \DateTimeImmutable()
        );
    });
