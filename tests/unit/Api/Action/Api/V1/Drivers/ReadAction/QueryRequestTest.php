<?php

namespace Tests\Unit\Api\Action\Api\V1\Drivers\ReadAction;

use App\Api\Action\Api\V1\Drivers\ReadAction\QueryRequest;
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

    public function testConstructorNoRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new QueryRequest(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal()
        );
    }

    public function testGetCriteriaReturnArray(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $string = '209b82cb-6f17-4020-ace4-54f6bbecd388';
        $this->requestMock->get('id')
            ->shouldBeCalled()
            ->willReturn($string);

        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $client1CIdValue = 'clientId';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($client1CIdValue);

        $queryRequest = new QueryRequest(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal()
        );

        $expected = [
            'id_equalTo' => $string,
            'client1CId_equalTo' => $client1CIdValue,
        ];

        $result = $queryRequest->getCriteria();

        $this->assertEquals($expected, $result);
    }
}
