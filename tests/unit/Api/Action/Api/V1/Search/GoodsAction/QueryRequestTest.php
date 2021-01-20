<?php

namespace Tests\Unit\Api\Action\Api\V1\Search\GoodsAction;

use App\Api\Action\Api\V1\Search\GoodsAction\QueryRequest;
use App\Clients\Infrastructure\Fuel\Criteria\Search;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class QueryRequestTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|Request
     */
    private $requestMock;

    protected function setUp(): void
    {
        $this->requestMock = $this->prophesize(Request::class);
    }

    public function testGetCriteriaEmptyQParameterReturnBaseCriteria(): void
    {
        $this->requestMock->get('q')
            ->shouldBeCalled()
            ->willReturn('');

        $queryRequest = new QueryRequest($this->requestMock->reveal());
        $result = $queryRequest->getCriteria();

        $this->assertEquals([
            'fuelType_equalTo' => 2,
            'purseCode_greaterThan' => 0,
        ], $result);
    }

    public function testGetCriteriaNullQParameterReturnBaseCriteria(): void
    {
        $this->requestMock->get('q')
            ->shouldBeCalled()
            ->willReturn(null);

        $queryRequest = new QueryRequest($this->requestMock->reveal());
        $result = $queryRequest->getCriteria();

        $this->assertEquals([
            'fuelType_equalTo' => 2,
            'purseCode_greaterThan' => 0,
        ], $result);
    }

    public function testGetCriteriaNotEmptyQParameterReturnCriteria(): void
    {
        $this->requestMock->get('q')
            ->shouldBeCalled()
            ->willReturn('text');

        $queryRequest = new QueryRequest($this->requestMock->reveal());
        $result = $queryRequest->getCriteria();

        $this->assertEquals([
            'fuelType_equalTo' => 2,
            'purseCode_greaterThan' => 0,
            Search::class => 'text',
        ], $result);
    }

    public function testGetQueryParamsRequestWithoutLimitReturnDefaultLimit(): void
    {
        $request = Request::create('/', 'GET');

        $queryRequest = new QueryRequest($request);
        $result = $queryRequest->getQueryParams();

        $this->assertEquals([
            'limit' => 100,
            'offset' => 0,
        ], $result);
    }

    public function testGetQueryParamsRequestWithLimitReturnLimit(): void
    {
        $limitValue = 10;
        $this->requestMock->get('limit', 100)
            ->shouldBeCalled()
            ->willReturn($limitValue);
        $offsetValue = 5;
        $this->requestMock->get('offset', 0)
            ->shouldBeCalled()
            ->willReturn($offsetValue);

        $queryRequest = new QueryRequest($this->requestMock->reveal());
        $result = $queryRequest->getQueryParams();

        $this->assertEquals([
            'limit' => $limitValue,
            'offset' => $offsetValue,
        ], $result);
    }
}
