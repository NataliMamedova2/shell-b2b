<?php

namespace Tests\Unit\Clients\Infrastructure\Invoice\Service;

use App\Clients\Domain\ShellInformation\ShellInformation;
use App\Clients\Infrastructure\Invoice\Service\NumberGenerator;
use Infrastructure\Exception\InvalidArgumentException;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;

final class NumberGeneratorTest extends TestCase
{
    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $shellInfoRepositoryMock;
    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $invoiceRepositoryMock;

    /**
     * @var NumberGenerator
     */
    private $service;

    protected function setUp(): void
    {
        $this->shellInfoRepositoryMock = $this->prophesize(Repository::class);
        $this->invoiceRepositoryMock = $this->prophesize(Repository::class);

        $this->service = new NumberGenerator($this->shellInfoRepositoryMock->reveal(), $this->invoiceRepositoryMock->reveal());
    }

    public function testNextShellInfoNotFoundReturnException(): void
    {
        $this->shellInfoRepositoryMock->find([])
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(InvalidArgumentException::class);

        $this->service->next();
    }

    public function testNextShellInfoFoundNumberReturnNumber(): void
    {
        $shellInfoMock = $this->prophesize(ShellInformation::class);
        $this->shellInfoRepositoryMock->find([])
            ->shouldBeCalled()
            ->willReturn($shellInfoMock);

        $countInvoices = 0;
        $this->invoiceRepositoryMock->count()
            ->shouldBeCalled()
            ->willReturn($countInvoices);

        $prefix = 'WWW';
        $shellInfoMock->getInvoiceNumberPrefix()
            ->shouldBeCalled()
            ->willReturn($prefix);

        $result = $this->service->next();

        $this->assertEquals($prefix.'020000', $result);
    }
}
