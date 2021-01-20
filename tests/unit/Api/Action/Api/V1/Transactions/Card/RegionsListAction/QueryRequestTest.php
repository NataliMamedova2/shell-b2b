<?php

namespace Tests\Unit\Api\Action\Api\V1\Transactions\Card\RegionsListAction;

use App\Api\Action\Api\V1\Transactions\Card\RegionsListAction\QueryRequest;
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

    public function testGetOrderReturnArray(): void
    {
        $queryRequest = $this->validQueryRequest();

        $expectOrder = ['name' => 'ASC'];
        $this->assertEquals($expectOrder, $queryRequest->getOrder());
    }

    private function validQueryRequest(): QueryRequest
    {
        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        return new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
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
