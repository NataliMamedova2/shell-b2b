<?php

namespace Tests\Unit\Api\Action\Api\V1\Transactions\Card\CreateReport;

use App\Api\Action\Api\V1\Transactions\Card\CreateReportAction\QueryRequest;
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

        $request = Request::create('/', 'GET');
        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($request);

        $queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());

        $date = new \DateTime('-1 month');
        $date->setTime(0, 0, 0);

        $expectCriteria = [
            'client1CId_equalTo' => $clientId,
            'postDate_greaterThanOrEqualTo' => $date,
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

    public function testGetQueryParamsRequestWithoutFilterReturnData(): void
    {
        $request = Request::create('/', 'GET');
        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($request);

        $queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());

        $result = $queryRequest->getQueryParams();

        $this->assertEquals([
            'dateFrom' => date('Y-m-d', strtotime('-1 month')),
            'dateTo' => date('Y-m-d'),
            'cardNumber' => '',
            'suppliesCodes' => [],
            'status' => '',
        ], $result);
    }

    public function testGetQueryParamsRequestWithFiltersReturnData(): void
    {
        $dateForm = '2012-02-12';
        $this->requestMock->get('dateFrom', date('Y-m-d', strtotime('-1 month')))
            ->shouldBeCalled()
            ->willReturn($dateForm);

        $dateTo = '2012-01-12';
        $this->requestMock->get('dateTo', date('Y-m-d'))
            ->shouldBeCalled()
            ->willReturn($dateTo);

        $cardNumber = 'cardNumber';
        $this->requestMock->get('cardNumber', '')
            ->shouldBeCalled()
            ->willReturn($cardNumber);

        $suppliesValue = ['КВЦ0000004'];
        $this->requestMock->get('supplies', [])
            ->shouldBeCalled()
            ->willReturn($suppliesValue);

        $status = 'write-off';
        $this->requestMock->get('status', '')
            ->shouldBeCalled()
            ->willReturn($status);

        $queryRequest = $this->validQueryRequest();
        $result = $queryRequest->getQueryParams();

        $this->assertEquals([
            'dateFrom' => $dateForm,
            'dateTo' => $dateTo,
            'cardNumber' => $cardNumber,
            'suppliesCodes' => $suppliesValue,
            'status' => $status,
        ], $result);
    }
}
