<?php

namespace Tests\Unit\Clients\Domain\Document;

use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Document\Document;
use App\Clients\Domain\Document\ValueObject\File;
use App\Clients\Domain\Document\ValueObject\Status;
use App\Clients\Domain\Document\ValueObject\Type;
use App\Clients\Domain\Invoice\Invoice;
use PHPUnit\Framework\TestCase;

final class DocumentTest extends TestCase
{
    public function testCreateReturnObject(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = IdentityId::fromString($string);

        $clientMock = $this->prophesize(Client::class);
        $clientIdValue = 'clientId';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($clientIdValue);

        $file = new File('file/path/', 'filename', 'ext');

        $actCheckingTypeValue = 1;
        $type = new Type($actCheckingTypeValue);

        $formedByRequestStatusValue = 1;
        $status = new Status($formedByRequestStatusValue);

        $dateTime = new \DateTimeImmutable('2019-01-01 12:12:12');
        $result = Document::create(
            $identity,
            $clientMock->reveal(),
            $file,
            $type,
            $status,
            $dateTime
        );

        $this->assertEmpty($result->getNumber());
        $this->assertEmpty($result->getAmount());
        $this->assertEquals($string, $result->getId());
        $this->assertEquals($file, $result->getFile());
        $this->assertEquals($actCheckingTypeValue, $result->getType());
        $this->assertEquals($formedByRequestStatusValue, $result->getStatus());
        $this->assertEquals($dateTime, $result->getCreatedAt());
    }

    public function testCreateFromInvoiceReturnObject(): void
    {
        $invoiceMock = $this->prophesize(Invoice::class);
        $file = new File('file/path/', 'filename', 'ext');
        $dateTime = new \DateTimeImmutable('2019-01-01 12:12:12');

        $clientId = 'clientId1C';
        $invoiceMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($clientId);

        $number = 'invoice_number';
        $invoiceMock->getNumber()
            ->shouldBeCalled()
            ->willReturn($number);

        $totalValue = 1200;
        $invoiceMock->getTotalWithValueTax()
            ->shouldBeCalled()
            ->willReturn($totalValue);

        $result = Document::createFromInvoice($invoiceMock->reveal(), $file, $dateTime);

        $this->assertEquals($number, $result->getNumber());
        $this->assertEquals($file, $result->getFile());
        $this->assertEquals($totalValue, $result->getAmount());

        $invoiceType = 0;
        $this->assertEquals($invoiceType, $result->getType());

        $formedByRequestStatus = 1;
        $this->assertEquals($formedByRequestStatus, $result->getStatus());
        $this->assertEquals($dateTime, $result->getCreatedAt());
    }

    public function testCreateUploadedDocumentReturnObject(): void
    {
        $clientIdValue = 'clientId1C';
        $clientObject = new Client1CId($clientIdValue);
        $typeValue = 2;
        $type = new Type($typeValue);
        $file = new File('file/path', 'filename', 'ext');
        $dateTime = new \DateTimeImmutable('2019-01-01 12:12:12');

        $result = Document::createUploadedDocument($clientObject, $type, $file, $dateTime);

        $this->assertEquals(null, $result->getNumber());
        $this->assertEquals(null, $result->getAmount());
        $this->assertEquals($file, $result->getFile());
        $this->assertEquals($typeValue, $result->getType());

        $formedByRequestStatus = 0;
        $this->assertEquals($formedByRequestStatus, $result->getStatus());
        $this->assertEquals($dateTime, $result->getCreatedAt());
    }
}
