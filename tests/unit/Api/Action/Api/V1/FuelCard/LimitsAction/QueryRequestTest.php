<?php

namespace Tests\Unit\Api\Action\Api\V1\FuelCard\LimitsAction;

use App\Api\Action\Api\V1\FuelCard\LimitsAction\QueryRequest;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Infrastructure\Fuel\Criteria\FuelLimitByType;
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
    /**
     * @var QueryRequest
     */
    private $queryRequest;

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

    public function testConstructEmptyCurrentRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
    }

    public function testGetCriteriaNoTypeReturnArrayDefaultType(): void
    {
        $defaultType = FuelType::fuel()->getName();

        $this->requestMock->get('type', $defaultType)
            ->shouldBeCalled()
            ->willReturn($defaultType);

        $result = $this->queryRequest->getCriteria();

        $expectCriteria = [
            FuelLimitByType::class => FuelType::fromName($defaultType),
        ];

        $this->assertEquals($expectCriteria, $result);
    }

    public function testGetCriteriaRequestHasTypeReturnArrayWithType(): void
    {
        $defaultType = FuelType::fuel()->getName();
        $typeValue = 'goods';
        $this->requestMock->get('type', $defaultType)
            ->shouldBeCalled()
            ->willReturn($typeValue);

        $result = $this->queryRequest->getCriteria();

        $expectCriteria = [
            FuelLimitByType::class => FuelType::fromName($typeValue),
        ];

        $this->assertEquals($expectCriteria, $result);
    }

    public function testGetOrderReturnEmptyArray(): void
    {
        $result = $this->queryRequest->getOrder();

        $expectOrder = [];
        $this->assertEquals($expectOrder, $result);
    }

    public function testGetQueryParamsReturnArrayWithCardId(): void
    {
        $cardId = 12;
        $this->requestMock->get('id')
            ->shouldBeCalled()
            ->willReturn($cardId);

        $result = $this->queryRequest->getQueryParams();

        $expectParams = [
            'cardId' => $cardId,
        ];
        $this->assertEquals($expectParams, $result);
    }
}
