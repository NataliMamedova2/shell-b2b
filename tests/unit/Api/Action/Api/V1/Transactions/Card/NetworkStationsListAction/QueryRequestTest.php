<?php

namespace Tests\Unit\Api\Action\Api\V1\Transactions\Card\NetworkStationsListAction;

use App\Api\Action\Api\V1\Transactions\Card\NetworkStationsListAction\QueryRequest;
use App\Clients\Domain\Client\Client;
use App\Clients\Infrastructure\Transaction\Criteria\NetworkStation\SearchNameCriteria;
use App\Security\Cabinet\MyselfInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class QueryRequestTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|RequestStack
     */
    private $requestStackMock;
    /**
     * @var MyselfInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $myselfMock;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|Request
     */
    private $requestMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->requestMock = $this->prophesize(Request::class);
    }

    public function testConstructEmptyRequestReturnException(): void
    {
        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
    }

    /**
     * @param $value
     * @dataProvider providerEmptyQParam
     */
    public function testGetCriteriaEmptyQueryParamsReturnDefaultCriteria($value): void
    {
        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $clientId = 'cl-id';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($clientId);

        $this->requestMock->get('q')
            ->shouldBeCalled()
            ->willReturn($value);

        $queryRequest = $this->validQueryRequest();

        $expectCriteria = [
            'client1CId_equalTo' => $clientId,
        ];
        $this->assertEquals($expectCriteria, $queryRequest->getCriteria());
    }

    public function providerEmptyQParam()
    {
        return [
            'no "q" param' => [null],
            'empty "q" param' => [''],
        ];
    }

    public function testGetCriteriaHasQueryParamReturnArray(): void
    {
        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $clientId = 'cl-id';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($clientId);

        $searchText = 'text';
        $this->requestMock->get('q')
            ->shouldBeCalled()
            ->willReturn($searchText);

        $queryRequest = $this->validQueryRequest();

        $expectCriteria = [
            'client1CId_equalTo' => $clientId,
            SearchNameCriteria::class => $searchText,
        ];

        $this->assertEquals($expectCriteria, $queryRequest->getCriteria());
    }

    private function validQueryRequest(): QueryRequest
    {
        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        return new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
    }

    public function testGetOrderReturnArray(): void
    {
        $queryRequest = $this->validQueryRequest();

        $expectOrder = ['name' => 'ASC'];
        $this->assertEquals($expectOrder, $queryRequest->getOrder());
    }

    public function testGetQueryParamsRequestWithoutLimitReturnDefaultLimitOffset(): void
    {
        $request = Request::create('/', 'GET');
        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($request);

        $queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());

        $result = $queryRequest->getQueryParams();

        $defaultLimit = 100;
        $defaultOffset = 0;
        $this->assertEquals([
            'limit' => $defaultLimit,
            'offset' => $defaultOffset,
        ], $result);
    }

    public function testGetQueryParamsRequestWithLimitReturnLimitOffset(): void
    {
        $defaultLimit = 100;
        $this->requestMock->get('limit', $defaultLimit)
            ->shouldBeCalled()
            ->willReturn(10);
        $defaultOffset = 0;
        $this->requestMock->get('offset', $defaultOffset)
            ->shouldBeCalled()
            ->willReturn(5);

        $queryRequest = $this->validQueryRequest();
        $result = $queryRequest->getQueryParams();

        $this->assertEquals([
            'limit' => 10,
            'offset' => 5,
        ], $result);
    }
}
