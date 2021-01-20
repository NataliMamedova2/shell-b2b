<?php

namespace Tests\Unit\Api\Action\Api\V1\Invoice\CreditDebtAction;

use App\Api\Action\Api\V1\Invoice\CreditDebtAction\CreditDebtService;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Infrastructure\ClientInfo\Criteria\ByClient;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\Client\ClientTest;
use Tests\Unit\Clients\Domain\ClientInfo\ClientInfoTest;

final class CreditDebtServiceTest extends TestCase
{
    /**
     * @var MyselfInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $myselfMock;
    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $clientInfoRepositoryMock;
    /**
     * @var QueryRequest|\Prophecy\Prophecy\ObjectProphecy
     */
    private $queryRequestMock;
    /**
     * @var CreditDebtService
     */
    private $service;

    protected function setUp(): void
    {
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->clientInfoRepositoryMock = $this->prophesize(Repository::class);
        $this->queryRequestMock = $this->prophesize(QueryRequest::class);

        $this->service = new CreditDebtService($this->myselfMock->reveal(), $this->clientInfoRepositoryMock->reveal());
    }

    public function testHandleClientInfoNotFoundReturnZero(): void
    {
        $client = ClientTest::createValidEntity();
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $this->clientInfoRepositoryMock->find([
            ByClient::class => $client,
        ])
        ->shouldBeCalled()
        ->willReturn(null);

        $result = $this->service->handle($this->queryRequestMock->reveal());

        $expected = [
            'amount' => 0,
        ];
        $this->assertEquals($expected, $result);
    }

    public function testHandleClientInfoBalanceGreaterThanZeroReturnZero(): void
    {
        $client = ClientTest::createValidEntity();
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $clientInfoMock = $this->prophesize(ClientInfo::class);
        $this->clientInfoRepositoryMock->find([
            ByClient::class => $client,
        ])
            ->shouldBeCalled()
            ->willReturn($clientInfoMock->reveal());

        $balance = 100;
        $clientInfoMock->getBalance()
            ->shouldBeCalled()
            ->willReturn($balance);

        $result = $this->service->handle($this->queryRequestMock->reveal());

        $expected = [
            'amount' => 0,
        ];
        $this->assertEquals($expected, $result);
    }

    public function testHandleClientInfoBalanceZeroReturnZero(): void
    {
        $client = ClientTest::createValidEntity();
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $clientInfoMock = $this->prophesize(ClientInfo::class);
        $this->clientInfoRepositoryMock->find([
            ByClient::class => $client,
        ])
            ->shouldBeCalled()
            ->willReturn($clientInfoMock->reveal());

        $balance = 0;
        $clientInfoMock->getBalance()
            ->shouldBeCalled()
            ->willReturn($balance);

        $result = $this->service->handle($this->queryRequestMock->reveal());

        $expected = [
            'amount' => 0,
        ];
        $this->assertEquals($expected, $result);
    }

    public function testHandleClientInfoBalanceNegativeReturnAbsoluteAmount(): void
    {
        $client = ClientTest::createValidEntity();
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $clientInfoMock = $this->prophesize(ClientInfo::class);
        $this->clientInfoRepositoryMock->find([
            ByClient::class => $client,
        ])
            ->shouldBeCalled()
            ->willReturn($clientInfoMock->reveal());

        $balance = -1000;
        $absoluteBalance = 1000;
        $clientInfoMock->getBalance()
            ->shouldBeCalled()
            ->willReturn($balance);

        $result = $this->service->handle($this->queryRequestMock->reveal());

        $expected = [
            'amount' => $absoluteBalance,
        ];
        $this->assertEquals($expected, $result);
    }
}
