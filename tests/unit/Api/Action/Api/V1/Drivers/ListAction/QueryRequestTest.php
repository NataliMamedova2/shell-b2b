<?php

namespace Tests\Unit\Api\Action\Api\V1\Drivers\ListAction;

use App\Api\Action\Api\V1\Drivers\ListAction\QueryRequest;
use App\Clients\Domain\Client\Client;
use App\Clients\Infrastructure\Driver\Criteria\Search;
use App\Security\Cabinet\MyselfInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class QueryRequestTest extends TestCase
{
    /**
     * @var ObjectProphecy|RequestStack
     */
    private $requestStackMock;
    /**
     * @var MyselfInterface|ObjectProphecy
     */
    private $myselfMock;
    /**
     * @var ObjectProphecy|Request
     */
    private $requestMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->requestMock = $this->prophesize(Request::class);
    }

    public function testConstructorNoRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new QueryRequest(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal()
        );
    }

    public function testGetCriteriaNoRequestParamsReturnArray(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $this->requestMock->get('status')
            ->shouldBeCalled()
            ->willReturn(null);

        $this->requestMock->get('search')
            ->shouldBeCalled()
            ->willReturn(null);

        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $client1CIdValue = 'clientId';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($client1CIdValue);

        $queryRequest = new QueryRequest(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal()
        );

        $expected = [
            'client1CId_equalTo' => $client1CIdValue,
        ];

        $result = $queryRequest->getCriteria();

        $this->assertEquals($expected, $result);
    }

    public function testGetCriteriaStatusParamReturnArray(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $statusValue = 'active';
        $this->requestMock->get('status')
            ->shouldBeCalled()
            ->willReturn($statusValue);

        $this->requestMock->get('search')
            ->shouldBeCalled()
            ->willReturn(null);

        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $client1CIdValue = 'clientId';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($client1CIdValue);

        $queryRequest = new QueryRequest(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal()
        );

        $statusActiveValue = 1;
        $expected = [
            'client1CId_equalTo' => $client1CIdValue,
            'status_equalTo' => $statusActiveValue,
        ];

        $result = $queryRequest->getCriteria();

        $this->assertEquals($expected, $result);
    }

    public function testGetCriteriaStatusAndSearchParamReturnArray(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $statusValue = 'active';
        $this->requestMock->get('status')
            ->shouldBeCalled()
            ->willReturn($statusValue);

        $searchValue = 'search text';
        $this->requestMock->get('search')
            ->shouldBeCalled()
            ->willReturn($searchValue);

        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $client1CIdValue = 'clientId';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($client1CIdValue);

        $queryRequest = new QueryRequest(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal()
        );

        $statusActiveValue = 1;
        $expected = [
            'client1CId_equalTo' => $client1CIdValue,
            'status_equalTo' => $statusActiveValue,
            Search::class => $searchValue,
        ];

        $result = $queryRequest->getCriteria();

        $this->assertEquals($expected, $result);
    }
}
