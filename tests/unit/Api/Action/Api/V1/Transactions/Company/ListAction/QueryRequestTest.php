<?php

namespace Tests\Unit\Api\Action\Api\V1\Transactions\Company\ListAction;

use App\Api\Action\Api\V1\Transactions\Company\ListAction\QueryRequest;
use App\Clients\Infrastructure\Transaction\Criteria\ByClientCriteria;
use App\Security\Cabinet\MyselfInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Unit\Clients\Domain\Client\ClientTest;

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
     * @var QueryRequest
     */
    private $queryRequest;
    /**
     * @var ObjectProphecy|Request
     */
    private $requestMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->myselfMock = $this->prophesize(MyselfInterface::class);

        $this->requestMock = $this->prophesize(Request::class);
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $this->queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
    }

    public function testConstructorEmptyCurrentRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);
        $this->expectException(\InvalidArgumentException::class);

        new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
    }

    public function testConstructorValidCurrentRequest(): void
    {
        $request = $this->prophesize(Request::class);
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($request);

        $result = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());

        $this->assertInstanceOf(\App\Api\Crud\Interfaces\QueryRequest::class, $result);
    }

    public function testGetCriteriaReturnArray(): void
    {
        $client = ClientTest::createValidEntity();
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $result = $this->queryRequest->getCriteria();

        $criteria = [
            ByClientCriteria::class => $client,
        ];
        $this->assertEquals($criteria, $result);
    }

    public function testGetQueryParamsReturnArray(): void
    {
        $defaultValue = 1;
        $this->requestMock->get('page', $defaultValue)
            ->shouldBeCalled()
            ->willReturn(2);

        $result = $this->queryRequest->getQueryParams();

        $queryParams = [
            'page' => 2,
        ];
        $this->assertEquals($queryParams, $result);
    }
}
