<?php

namespace Tests\Unit\Clients\Domain\Document\ValueObject;

use App\Clients\Domain\Document\ValueObject\Type;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    /**
     * @param $value
     * @dataProvider validDataProvider
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new Type($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validDataProvider()
    {
        return [
            'invoice' => [0],
            'act-checking' => [1],
            'appendix-petroleum-products' => [2],
            'card-invoice' => [3],
            'acceptance-transfer-act' => [4],
        ];
    }

    public function testInvoiceReturnObject(): void
    {
        $invoiceValue = 0;
        $result = Type::invoice();

        $this->assertEquals($invoiceValue, $result->getValue());

        $invoiceName = 'invoice';
        $this->assertEquals($invoiceName, $result->getName());
        $this->assertEquals($invoiceValue, $result->__toString());
        $this->assertEquals($invoiceValue, (string) $result);
    }

    /**
     * @param $value
     * @dataProvider invalidDataProvider
     */
    public function testCreateInvalidValueReturnObject($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Type($value);
    }

    public function invalidDataProvider()
    {
        return [
            'negative' => [-1],
            'more than max' => [5],
        ];
    }
}
