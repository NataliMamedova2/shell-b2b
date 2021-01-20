<?php

namespace Tests\Unit\Import\Application\FileDataSaver;

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
use App\Import\Application\FileDataSaver\ClientDataSaver;
use App\Import\Application\FileDataSaver\FileDataSaverInterface;
use App\Import\Application\FileDataSaver\ShellInformationDataSaver;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ShellInformationDataSaverTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $entityManagerMock;

    /**
     * @var ClientDataSaver
     */
    private $serviceObject;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|LoggerInterface
     */
    private $loggerMock;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->loggerMock = $this->prophesize(LoggerInterface::class);
        $debug = false;

        $this->serviceObject = new ShellInformationDataSaver($this->entityManagerMock->reveal(), $this->loggerMock->reveal(), $debug);
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(FileDataSaverInterface::class, $this->serviceObject);
    }

    public function testSupportFileMethodReturnTrue(): void
    {
        $this->assertEquals(true, $this->serviceObject->supportedFile('filename.si'));
    }

    public function testSupportFileMethodReturnFalse(): void
    {
        $this->assertEquals(false, $this->serviceObject->supportedFile('filename.cc'));
    }

    public function testGetUniqueKeyFromEntityReturnNull(): void
    {
        $entity = new \stdClass();

        $result = $this->serviceObject->getUniqueKeyFromEntity($entity);
        $this->assertEquals(null, $result);
    }

    public function testGetUniqueKeyFromEntityReturnString(): void
    {
        $entity = self::createShellInformation();

        $result = $this->serviceObject->getUniqueKeyFromEntity($entity);
        $this->assertEquals('unique', $result);
    }

    public function testGetUniqueKeyFromRecordReturnString(): void
    {
        $result = $this->serviceObject->getUniqueKeyFromRecord([]);
        $this->assertEquals('unique', $result);
    }

    public function testCreateEntityReturnEntity(): void
    {
        $array = [
            'Товариство з обмеженою відповідальністю "Альянс Холдинг"', '34430873', '344308726590', '100330839',
            '0444950800', 'вул. М. Грінченка, б.4,Солом"янський район , м. Київ, 03680', '26003438796', '380805', 'ПАТ "Райффайзен Банк Аваль"', '', '', '2000', '3', 'WWW',
        ];

        $entity = self::createShellInformation($array);

        $result = $this->serviceObject->createEntity($array);

        $this->assertEquals($entity->getFullName(), $result->getFullName());
    }

    public function testUpdateEntityReturnEntity(): void
    {
        $fullName = 'Товариство з обмеженою відповідальністю "Альянс Холдинг"';
        $array = [
            'Товариство з обмеженою відповідальністю "Альянс Холдинг1212"', '34430873', '344308726590', '100330839',
            '0444950800', 'вул. М. Грінченка, б.4,Солом"янський район , м. Київ, 03680', '26003438796', '380805', 'ПАТ "Райффайзен Банк Аваль"', '', '', '2000', '3', 'WWW',
        ];

        $entity = self::createShellInformation([$fullName]);

        $result = $this->serviceObject->updateEntity($entity, $array);

        $this->assertNull($result);
    }

    private static function createShellInformation(array $array = []): ShellInformation
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ShellInformationId::fromString($string);

        $record = array_merge([
            'Товариство з обмеженою відповідальністю "Альянс Холдинг"', '34430873', '344308726590', '100330839',
            '0444950800', 'вул. М. Грінченка, б.4,Солом"янський район , м. Київ, 03680', '26003438796', '380805', 'ПАТ "Райффайзен Банк Аваль"', '', '', '2000', '3', 'WWW',
        ], $array);

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

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

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
