<?php

namespace Tests\Unit\Api\Action\Api\V1\Transactions\Company\ListAction;

use App\Api\Action\Api\V1\Transactions\Company\ListAction\ListService;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Clients\Infrastructure\ClientInfo\Service\Balance\Balance;
use App\Clients\Infrastructure\ClientInfo\Service\Balance\BalanceService;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;

final class ListServiceTest extends TestCase
{

    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $repositoryMock;
    /**
     * @var BalanceService|\Prophecy\Prophecy\ObjectProphecy
     */
    private $myBalanceMock;
    /**
     * @var QueryRequest|\Prophecy\Prophecy\ObjectProphecy
     */
    private $queryRequestMock;
    /**
     * @var ListService
     */
    private $service;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->prophesize(Repository::class);
        $this->myBalanceMock = $this->prophesize(BalanceService::class);

        $this->queryRequestMock = $this->prophesize(QueryRequest::class);

        $this->service = new ListService($this->repositoryMock->reveal(), $this->myBalanceMock->reveal());
    }

    public function testHandleEmptyQueryParamsReturnArray(): void
    {
        $params = [];
        $this->queryRequestMock->getQueryParams()
            ->shouldBeCalled()
            ->willReturn($params);

        $page = 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $criteria = [];
        $this->queryRequestMock->getCriteria()
            ->shouldBeCalled()
            ->willReturn($criteria);
        $order = [];
        $this->queryRequestMock->getOrder()
            ->shouldBeCalled()
            ->willReturn($criteria);

        $result = [];
        $this->repositoryMock->findMany($criteria, $order, $limit, $offset)
            ->shouldBeCalled()
            ->willReturn($result);
        $count = 0;
        $this->repositoryMock->count($criteria)
            ->shouldBeCalled()
            ->willReturn($count);

        $accountBalance = new Balance(0);
        $this->myBalanceMock->getBalance()
            ->shouldBeCalled()
            ->willReturn($accountBalance);

        $handleResult = $this->service->handle($this->queryRequestMock->reveal());

        $expected = [
            'result' => $result,
            'pageNumber' => $page,
            'countPages' => 1,
            'accountBalance' => $accountBalance,
        ];

        $this->assertEquals($expected, $handleResult);
    }
}
