<?php

namespace Tests\Unit\Api\Action\Api\V1\Transactions\Card\SuppliesListAction;

use App\Api\Action\Api\V1\Transactions\Card\SuppliesListAction\QueryRequest;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Infrastructure\Transaction\Repository\Repository;
use App\Clients\Infrastructure\Transaction\Repository\TransactionRepository;
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
     * @var TransactionRepository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $transactionRepositoryMock;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|Request
     */
    private $requestMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->transactionRepositoryMock = $this->prophesize(Repository::class);
        $this->requestMock = $this->prophesize(Request::class);
    }

    public function testConstructEmptyRequestReturnException(): void
    {
        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal(), $this->transactionRepositoryMock->reveal());
    }

    public function testGetCriteriaNoTypeParamReturnArray(): void
    {
        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $fuelTypeCodes = [
            'code-1',
            'code-2',
        ];
        $this->transactionRepositoryMock->getClientFuelCodes($clientMock->reveal())
            ->shouldBeCalled()
            ->willReturn($fuelTypeCodes);

        $this->requestMock->get('type')
            ->shouldBeCalled()
            ->willReturn([]);

        $queryRequest = $this->validQueryRequest();

        $expectCriteria = [
            'fuelCode_in' => $fuelTypeCodes,
        ];

        $this->assertEquals($expectCriteria, $queryRequest->getCriteria());
    }

    public function testGetCriteriaWithTypeParamReturnArray(): void
    {
        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $fuelTypeCodes = [
            'code-1',
            'code-2',
        ];
        $this->transactionRepositoryMock->getClientFuelCodes($clientMock->reveal())
            ->shouldBeCalled()
            ->willReturn($fuelTypeCodes);

        $typesNames = ['fuel', 'goods', 'service'];
        $this->requestMock->get('type')
            ->shouldBeCalled()
            ->willReturn($typesNames);

        $queryRequest = $this->validQueryRequest();

        $typesValues = [];
        foreach ($typesNames as $selectedType) {
            $typesValues[] = FuelType::fromName($selectedType)->getValue();
        }
        $expectCriteria = [
            'fuelCode_in' => $fuelTypeCodes,
            'fuelType_in' => $typesValues,
        ];

        $this->assertEquals($expectCriteria, $queryRequest->getCriteria());
    }

    public function testGetOrderReturnArray(): void
    {
        $queryRequest = $this->validQueryRequest();

        $expectOrder = ['fuelName' => 'ASC'];
        $this->assertEquals($expectOrder, $queryRequest->getOrder());
    }

    private function validQueryRequest(): QueryRequest
    {
        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        return new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal(), $this->transactionRepositoryMock->reveal());
    }

    public function testGetQueryParamsRequestWithoutLimitReturnDefaultLimitOffset(): void
    {
        $request = Request::create('/', 'GET');
        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($request);

        $queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal(), $this->transactionRepositoryMock->reveal());

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
