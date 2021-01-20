<?php

namespace App\Clients\Domain\ShellInformation;

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
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="shell_information")
 */
class ShellInformation
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\Column(name="full_name", type="string", nullable=false)
     */
    private $fullName;

    /**
     * @ORM\Column(name="zkpo", type="string", nullable=false)
     */
    private $zkpo;

    /**
     * @ORM\Column(name="ipn", type="string", nullable=false)
     */
    private $ipn;

    /**
     * @ORM\Column(name="certificate_number", type="string", nullable=false)
     */
    private $certificateNumber;

    /**
     * @ORM\Column(name="telephone_number", type="string", nullable=false)
     */
    private $telephoneNumber;

    /**
     * @ORM\Column(name="post_address", type="string", nullable=false)
     */
    private $postAddress;

    /**
     * @ORM\Column(name="current_account", type="string", nullable=false)
     */
    private $currentAccount;

    /**
     * @ORM\Column(name="current_mfo", type="string", nullable=false)
     */
    private $currentMfo;

    /**
     * @ORM\Column(name="current_bank", type="string", nullable=false)
     */
    private $currentBank;

    /**
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(name="site", type="string", nullable=true)
     */
    private $site;

    /**
     * @ORM\Column(name="nds", type="string", nullable=false)
     */
    private $nds;

    /**
     * @ORM\Column(name="invoice_valid_until_const", type="string", nullable=false)
     */
    private $invoiceValidUntilConst;

    /**
     * @ORM\Column(name="invoice_prename_const", type="string", nullable=false)
     */
    private $invoicePrenameConst;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $updatedAt;

    private function __construct(
        ShellInformationId $id,
        FullName $fullName,
        Zkpo $zkpo,
        Ipn $ipn,
        CertificateNumber $certificateNumber,
        TelephoneNumber $telephoneNumber,
        PostAddress $postAddress,
        CurrentAccount $currentAccount,
        CurrentMfo $currentMfo,
        CurrentBank $currentBank,
        Email $email,
        Site $site,
        Nds $nds,
        InvoiceValidUntilConst $invoiceValidUntilConst,
        InvoicePrenameConst $invoicePrenameConst
    ) {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->zkpo = $zkpo;
        $this->ipn = $ipn;
        $this->certificateNumber = $certificateNumber;
        $this->telephoneNumber = $telephoneNumber;
        $this->postAddress = $postAddress;
        $this->currentAccount = $currentAccount;
        $this->currentMfo = $currentMfo;
        $this->currentBank = $currentBank;
        $this->email = $email;
        $this->site = $site;
        $this->nds = $nds->getValue();
        $this->invoiceValidUntilConst = $invoiceValidUntilConst->getValue();
        $this->invoicePrenameConst = $invoicePrenameConst->getValue();
    }

    public static function create(
        ShellInformationId $id,
        FullName $fullName,
        Zkpo $zkpo,
        Ipn $ipn,
        CertificateNumber $certificateNumber,
        TelephoneNumber $telephoneNumber,
        PostAddress $postAddress,
        CurrentAccount $currentAccount,
        CurrentMfo $currentMfo,
        CurrentBank $currentBank,
        Email $email,
        Site $site,
        Nds $nds,
        InvoiceValidUntilConst $invoiceValidUntilConst,
        InvoicePrenameConst $invoicePrenameConst,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self(
            $id,
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

        $self->updatedAt = $createdAt;

        return $self;
    }

    public function update(
        FullName $fullName,
        Zkpo $zkpo,
        Ipn $ipn,
        CertificateNumber $certificateNumber,
        TelephoneNumber $telephoneNumber,
        PostAddress $postAddress,
        CurrentAccount $currentAccount,
        CurrentMfo $currentMfo,
        CurrentBank $currentBank,
        Email $email,
        Site $site,
        Nds $nds,
        InvoiceValidUntilConst $invoiceValidUntilConst,
        InvoicePrenameConst $invoicePrenameConst
    ): self {
        $this->fullName = $fullName;
        $this->zkpo = $zkpo;
        $this->ipn = $ipn;
        $this->certificateNumber = $certificateNumber;
        $this->telephoneNumber = $telephoneNumber;
        $this->postAddress = $postAddress;
        $this->currentAccount = $currentAccount;
        $this->currentMfo = $currentMfo;
        $this->currentBank = $currentBank;
        $this->email = $email;
        $this->site = $site;
        $this->nds = $nds->getValue();
        $this->invoiceValidUntilConst = $invoiceValidUntilConst->getValue();
        $this->invoicePrenameConst = $invoicePrenameConst->getValue();

        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getZkpo(): string
    {
        return $this->zkpo;
    }

    public function getIpn(): string
    {
        return $this->ipn;
    }

    public function getCertificateNumber(): string
    {
        return $this->certificateNumber;
    }

    public function getPhoneNumber(): string
    {
        return $this->telephoneNumber;
    }

    public function getPostAddress(): string
    {
        return $this->postAddress;
    }

    public function getCurrentAccount(): string
    {
        return $this->currentAccount;
    }

    public function getCurrentMfo(): string
    {
        return $this->currentMfo;
    }

    public function getCurrentBank(): string
    {
        return $this->currentBank;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSite(): string
    {
        return $this->site;
    }

    public function getInvoiceNumberPrefix(): string
    {
        return $this->invoicePrenameConst;
    }

    public function getValueAddedTax(): int
    {
        return (int) $this->nds;
    }

    public function getInvoiceValidDays(): int
    {
        return (int) $this->invoiceValidUntilConst;
    }
}
