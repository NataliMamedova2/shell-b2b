<?php

namespace Tests\Unit\Api\Action\Api\V1\Drivers\SearchAction;

use App\Api\Action\Api\V1\Drivers\SearchAction\QueryRequest;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Driver\ValueObject\Status;
use App\Clients\Infrastructure\Driver\Criteria\OrderByName;
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

    public function testGetCriteriaEmptyQParameterReturnBaseCriteria(): void
    {
        $this->requestMock->get('q')
            ->shouldBeCalled()
            ->willReturn('');

        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $client1CIdValue = 'clientId';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($client1CIdValue);

        $queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
        $result = $queryRequest->getCriteria();

        $expected = [
            'client1CId_equalTo' => $client1CIdValue,
            'status_equalTo' => Status::active()->getValue(),
        ];
        $this->assertEquals($expected, $result);
    }

    public function testGetCriteriaNullQParameterReturnBaseCriteria(): void
    {
        $this->requestMock->get('q')
            ->shouldBeCalled()
            ->willReturn(null);

        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $client1CIdValue = 'clientId';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($client1CIdValue);

        $queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
        $result = $queryRequest->getCriteria();

        $expected = [
            'client1CId_equalTo' => $client1CIdValue,
            'status_equalTo' => Status::active()->getValue(),
        ];
        $this->assertEquals($expected, $result);
    }

    public function testGetCriteriaNotEmptyQParameterReturnCriteria(): void
    {
        $queryText = 'text';
        $this->requestMock->get('q')
            ->shouldBeCalled()
            ->willReturn($queryText);

        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $client1CIdValue = 'clientId';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($client1CIdValue);

        $queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
        $result = $queryRequest->getCriteria();

        $expected = [
            Search::class => $queryText,
            'client1CId_equalTo' => $client1CIdValue,
            'status_equalTo' => Status::active()->getValue(),
        ];
        $this->assertEquals($expected, $result);
    }

    public function testGetOrderReturnArray(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
        $result = $queryRequest->getOrder();

        $expect = [OrderByName::class => 'ASC'];

        $this->assertEquals($expect, $result);
    }

    public function testGetQueryParamsRequestWithLimitReturnLimit(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $limitValue = 10;
        $this->requestMock->get('limit', 100)
            ->shouldBeCalled()
            ->willReturn($limitValue);
        $offsetValue = 5;
        $this->requestMock->get('offset', 0)
            ->shouldBeCalled()
            ->willReturn($offsetValue);

        $queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
        $result = $queryRequest->getQueryParams();

        $this->assertEquals([
            'limit' => $limitValue,
            'offset' => $offsetValue,
        ], $result);
    }
}
