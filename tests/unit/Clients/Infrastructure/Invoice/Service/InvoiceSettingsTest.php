<?php

namespace Tests\Unit\Clients\Infrastructure\Invoice\Service;

use App\Clients\Domain\ShellInformation\ShellInformation;
use App\Clients\Infrastructure\Invoice\Service\InvoiceSettings;
use Infrastructure\Exception\InvalidArgumentException;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;

final class InvoiceSettingsTest extends TestCase
{
    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $shellInfoRepositoryMock;

    protected function setUp(): void
    {
        $this->shellInfoRepositoryMock = $this->prophesize(Repository::class);
    }

    public function testConstructorShellInfoNotFoundReturnException(): void
    {
        $this->shellInfoRepositoryMock->find([])
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(InvalidArgumentException::class);

        new InvoiceSettings($this->shellInfoRepositoryMock->reveal());
    }

    public function testGetValueAddedTaxAndGetInvoiceValidDaysReturnInt(): void
    {
        $shellInfoMock = $this->prophesize(ShellInformation::class);
        $this->shellInfoRepositoryMock->find([])
            ->shouldBeCalled()
            ->willReturn($shellInfoMock);

        $valueAddedTax = 2000;
        $shellInfoMock->getValueAddedTax()
            ->shouldBeCalled()
            ->willReturn($valueAddedTax);

        $invoiceValidDays = 1;
        $shellInfoMock->getInvoiceValidDays()
            ->shouldBeCalled()
            ->willReturn($invoiceValidDays);

        $service = new InvoiceSettings($this->shellInfoRepositoryMock->reveal());

        $this->assertEquals($valueAddedTax, $service->getValueAddedTax());
        $this->assertEquals($invoiceValidDays, $service->getInvoiceValidDays());
    }
}
