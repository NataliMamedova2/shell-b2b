<?php
namespace Tests\Unit\Api\Action\Api\V1\Transactions\Card\ListAction;

use App\Api\Action\Api\V1\Transactions\Card\ListAction\QueryRequest;
use App\Clients\Domain\Client\Client;
use App\Security\Cabinet\MyselfInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class QueryRequestCardNumberCriteriaTest extends TestCase
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
     * @var \App\Api\Crud\Interfaces\QueryRequest
     */
    private $queryRequest;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->requestMock = $this->prophesize(Request::class);

        $this->requestStackMock
            ->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $this->requestMock->get('dateFrom')
            ->shouldBeCalled();
        $this->requestMock->get('dateTo')
            ->shouldBeCalled();
        $this->requestMock->get('regions')
            ->shouldBeCalled();
        $this->requestMock->get('supplyTypes')
            ->shouldBeCalled();
        $this->requestMock->get('supplies')
            ->shouldBeCalled();
        $this->requestMock->get('networkStations')
            ->shouldBeCalled();
        $this->requestMock->get('status')
            ->shouldBeCalled();

        $this->queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->myselfMock->reveal());
    }

    /**
     * @param $value
     * @dataProvider providerEmptyCardNumberParam
     */
    public function testGetCriteriaEmptyCardNumberReturnDefaultCriteria($value): void
    {
        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $clientId = 'cl-id';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($clientId);

        $this->requestMock->get('cardNumber')
            ->shouldBeCalled()
            ->willReturn($value);

        $expectCriteria = [
            'client1CId_equalTo' => $clientId,
        ];
        $this->assertEquals($expectCriteria, $this->queryRequest->getCriteria());
    }

    public function providerEmptyCardNumberParam()
    {
        return [
            'null "cardNumber" param' => [null],
            'empty "cardNumber" param' => [''],
        ];
    }

    /**
     * @param $value
     * @dataProvider providerNotEmptyCardNumberParam
     */
    public function testGetCriteriaNotEmtyCardNumberReturnDefaultCriteria($value): void
    {
        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $clientId = 'cl-id';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($clientId);

        $this->requestMock->get('cardNumber')
            ->shouldBeCalled()
            ->willReturn($value);

        $expectCriteria = [
            'client1CId_equalTo' => $clientId,
            'cardNumber_like' => "%$value%",
        ];
        $this->assertEquals($expectCriteria, $this->queryRequest->getCriteria());
    }

    public function providerNotEmptyCardNumberParam()
    {
        return [
            'string "cardNumber" param' => ['111'],
            'integer "cardNumber" param' => [12],
        ];
    }
}