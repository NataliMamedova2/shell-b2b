<?php

namespace Tests\Unit\Clients\Domain\Invoice;

use App\Application\Domain\ValueObject\FuelCode;
use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\Invoice\Item;
use App\Clients\Domain\Invoice\ValueObject\ItemPrice;
use App\Clients\Domain\Invoice\ValueObject\LineNumber;
use App\Clients\Domain\Invoice\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = IdentityId::fromString($string);

        $invoice = InvoiceTest::createValidEntity();

        $lineNumber = new LineNumber(1);
        $fuelCode = new FuelCode('FC000001');
        $priceValue = 32;
        $price = new ItemPrice($priceValue);
        $quantityValue = 25;
        $quantity = new Quantity($quantityValue);

        $date = new \DateTimeImmutable('2019-01-01 00:00:00');

        $entity = new Item(
            $identity,
            $invoice,
            $lineNumber,
            $fuelCode,
            $price,
            $quantity,
            $date
        );

        $pdv = $invoice->getValueTax() / 100;

        $priceSPDV = $priceValue * 100;
        $priceBPDV = ceil(($priceValue / (100 + $pdv) * 100) * 10000000);
        $sumSPDB = ($quantityValue * $priceValue) * 100;
        $sumPDB = ($quantityValue * $priceValue / (100 + $pdv) * $pdv) * 100;
        $sumBPDB = ($quantityValue * $priceValue / (100 + $pdv) * 100) * 100;

        $this->assertEquals((string) $fuelCode, (string) $entity->getFuelCode());
        $this->assertEquals(1, (string) $entity->getLineNumber());
        $this->assertEquals($quantityValue * 100, $entity->getQuantity());
        $this->assertEquals($priceSPDV, $entity->getPriceWithValueTax());
        $this->assertEquals($priceBPDV, $entity->getPriceWithoutValueTax());
        $this->assertEquals($sumSPDB, $entity->getSumWithValueTax());
        $this->assertEquals($sumPDB, $entity->getSumValueTax());
        $this->assertEquals($sumBPDB, $entity->getSumWithoutValueTax());
    }
}
