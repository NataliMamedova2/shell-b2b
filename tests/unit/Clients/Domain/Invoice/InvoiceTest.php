<?php

namespace Tests\Unit\Clients\Domain\Invoice;

use App\Application\Domain\ValueObject\FuelCode;
use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\Invoice\Invoice;
use App\Clients\Domain\Invoice\ValueObject\Date;
use App\Clients\Domain\Invoice\ValueObject\InvoiceNumber;
use App\Clients\Domain\Invoice\ValueObject\ItemPrice;
use App\Clients\Domain\Invoice\ValueObject\LineNumber;
use App\Clients\Domain\Invoice\ValueObject\Quantity;
use App\Clients\Domain\Invoice\ValueObject\ValueTax;
use Doctrine\Common\Collections\ArrayCollection;
use Domain\Exception\DomainException;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\Client\ClientTest;

final class InvoiceTest extends TestCase
{
    public function testCreateNoItems(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = IdentityId::fromString($string);

        $client = ClientTest::createValidEntity();

        $valueTaxValue = 20;
        $numberValue = 'WWW000001';
        $invoiceNumber = new InvoiceNumber($numberValue);
        $valueTax = new ValueTax($valueTaxValue);

        $creationDate = new \DateTimeImmutable('2019-01-01 00:00:00');
        $date = new Date($creationDate, 3);

        $entity = Invoice::create(
            $identity,
            $client->getClient1CId(),
            $invoiceNumber,
            $valueTax,
            $date
        );

        $this->assertEquals(new ArrayCollection([]), $entity->getItems());
        $this->assertEquals($client->getClient1CId(), $entity->getClient1CId());
        $this->assertEquals("СФ-{$numberValue}", $entity->getNumber());
        $this->assertEquals(date('Y')."СФ-{$numberValue}", $entity->getInvoiceId());
        $this->assertEquals($creationDate, $entity->getDate()->getCreationDate());
        $this->assertEquals(new \DateTimeImmutable('2019-01-04 00:00:00'), $entity->getDate()->getExpirationDate());

        $this->assertEquals($valueTaxValue * 100, $entity->getValueTax());
        $this->assertEquals(0, $entity->getTotalWithoutValueTax());
        $this->assertEquals(0, $entity->getTotalValueTax());
        $this->assertEquals(0, $entity->getTotalWithValueTax());
    }

    public function testAddItemItemAlreadyAddedReturnException(): void
    {
        $entity = self::createValidEntity();

        $lineNumber = new LineNumber(1);
        $fuelCode = new FuelCode('FC000001');
        $priceValue = 32;
        $price = new ItemPrice($priceValue);
        $quantityValue = 25;
        $quantity = new Quantity($quantityValue);
        $creationDate = new \DateTimeImmutable('2019-01-01 00:00:00');

        $entity->addItem($lineNumber, $fuelCode, $price, $quantity, $creationDate);
        $this->assertEquals(1, count($entity->getItems()));

        $pdv = $entity->getValueTax() / 100;

        // ===== totalBPDV
        $totalBPDV = 0;
        $item1_sumBPDB = ($quantityValue * $priceValue / (100 + $pdv) * 100) * 100;
        $totalBPDV += $item1_sumBPDB;

        $this->assertEquals($totalBPDV, $entity->getTotalWithoutValueTax());

        // ===== totalPDV
        $totalPDV = 0;
        $item1_sumPDB = ($quantityValue * $priceValue / (100 + $pdv) * $pdv) * 100;
        $totalPDV += $item1_sumPDB;

        $this->assertEquals($totalPDV, $entity->getTotalValueTax());

        // ===== totalSPDV
        $totalSPDV = 0;
        $item1_sumSPDB = ($quantityValue * $priceValue) * 100;
        $totalSPDV += $item1_sumSPDB;

        $this->assertEquals($totalSPDV, $entity->getTotalWithValueTax());

        $this->expectException(DomainException::class);
        $entity->addItem($lineNumber, $fuelCode, $price, $quantity, $creationDate);
    }

    public static function createValidEntity(): Invoice
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = IdentityId::fromString($string);

        $client = ClientTest::createValidEntity();

        $valueTaxValue = 20;
        $numberValue = 'WWW000001';
        $invoiceNumber = new InvoiceNumber($numberValue);
        $valueTax = new ValueTax($valueTaxValue);

        $creationDate = new \DateTimeImmutable('2019-01-01 00:00:00');
        $date = new Date($creationDate, 3);

        return Invoice::create(
            $identity,
            $client->getClient1CId(),
            $invoiceNumber,
            $valueTax,
            $date
        );
    }
}
