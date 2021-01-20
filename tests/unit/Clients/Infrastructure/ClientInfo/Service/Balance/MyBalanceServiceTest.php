<?php

namespace Tests\Unit\Clients\Infrastructure\ClientInfo\Service\Balance;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Infrastructure\ClientInfo\Criteria\ByClient;
use App\Clients\Infrastructure\ClientInfo\Service\Balance\MyBalanceService;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class MyBalanceServiceTest extends TestCase
{
    /**
     * @var Repository|ObjectProphecy
     */
    private $clientInfoRepositoryMock;
    /**
     * @var MyselfInterface|ObjectProphecy
     */
    private $myselfMock;
    /**
     * @var MyBalanceService
     */
    private $service;

    protected function setUp(): void
    {
        $this->clientInfoRepositoryMock = $this->prophesize(Repository::class);
        $this->myselfMock = $this->prophesize(MyselfInterface::class);

        $this->service = new  MyBalanceService($this->clientInfoRepositoryMock->reveal(), $this->myselfMock->reveal());
    }

    public function testGetBalanceNullClientInfoReturnBalance(): void
    {
        $client = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $this->clientInfoRepositoryMock->find([
            ByClient::class => $client,
        ])
            ->shouldBeCalled()
            ->willReturn(null);

        $result = $this->service->getBalance();

        $this->assertEquals(0, $result->getValue());
        $this->assertEquals(0, $result->getAbsoluteValue());
        $this->assertEquals('+', $result->getSign());
    }

    public function testGetBalanceClientInfoReturnBalance(): void
    {
        $client = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $clientInfoMock = $this->prophesize(ClientInfo::class);
        $this->clientInfoRepositoryMock->find([
            ByClient::class => $client,
        ])
            ->shouldBeCalled()
            ->willReturn($clientInfoMock->reveal());

        $balanceValue = -122345;
        $clientInfoMock->getBalance()
            ->shouldBeCalled()
            ->willReturn($balanceValue);

        $result = $this->service->getBalance();

        $this->assertEquals(-122345, $result->getValue());
        $this->assertEquals(122345, $result->getAbsoluteValue());
        $this->assertEquals('-', $result->getSign());
    }
}
