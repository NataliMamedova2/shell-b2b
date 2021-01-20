<?php
namespace Tests\Unit\Api\Action\Api\V1\Transactions\Card\ListAction;

use App\Api\Action\Api\V1\Transactions\Card\ListAction\QueryRequest;
use App\Clients\Domain\Client\Client;
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

    public function testConstructEmptyRequestReturnException(): void
    {
        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
    }

    public function testGetCriteriaNoQueryParamsReturnDefaultCriteria(): void
    {
        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $clientId = 'client-id';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($clientId);

        $queryRequest = $this->validQueryRequest();

        $expectCriteria = [
            'client1CId_equalTo' => $clientId,
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

        $expectOrder = ['createdAt' => 'desc'];
        $this->assertEquals($expectOrder, $queryRequest->getOrder());
    }

    public function testGetQueryParamsRequestWithoutPageReturnDefaultPage(): void
    {
        $request = Request::create('/', 'GET');
        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($request);

        $queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());

        $result = $queryRequest->getQueryParams();

        $defaultPage = 1;
        $this->assertEquals([
            'page' => $defaultPage,
            'supplies' => [],
            'regions' => [],
            'networkStations' => [],
        ], $result);
    }

    public function testGetQueryParamsRequestWithPageReturnPage(): void
    {
        $defaultPage = 1;
        $pageValue = 10;
        $this->requestMock->get('page', $defaultPage)
            ->shouldBeCalled()
            ->willReturn($pageValue);

        $suppliesValue = ['КВЦ0000004'];
        $this->requestMock->get('supplies', [])
            ->shouldBeCalled()
            ->willReturn($suppliesValue);

        $regionsValue = ['КВЦ0000515', 'КВЦ0000542'];
        $this->requestMock->get('regions', [])
            ->shouldBeCalled()
            ->willReturn($regionsValue);

        $networkStationsValue = ['КВЦ0000569'];
        $this->requestMock->get('networkStations', [])
            ->shouldBeCalled()
            ->willReturn($networkStationsValue);

        $queryRequest = $this->validQueryRequest();
        $result = $queryRequest->getQueryParams();

        $this->assertEquals([
            'page' => $pageValue,
            'supplies' => $suppliesValue,
            'regions' => $regionsValue,
            'networkStations' => $networkStationsValue,
        ], $result);
    }
}