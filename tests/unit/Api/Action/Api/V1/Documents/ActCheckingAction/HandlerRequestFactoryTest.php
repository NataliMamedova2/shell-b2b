<?php

namespace Tests\Unit\Api\Action\Api\V1\Documents\ActCheckingAction;

use App\Api\Action\Api\V1\Documents\ActCheckingAction\HandlerRequestFactory;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Document\UseCase\ActChecking\HandlerRequest;
use App\Security\Cabinet\MyselfInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class HandlerRequestFactoryTest extends TestCase
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

    public function testConstructorEmptyRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new HandlerRequestFactory($this->requestStackMock->reveal(), $this->myselfMock->reveal());
    }

    public function testInvokeReturnHandlerRequest(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $dateFromValue = '2020-01';
        $this->requestMock->get('dateFrom')
            ->shouldBeCalled()
            ->willReturn($dateFromValue);

        $dateToValue = '2020-03';
        $this->requestMock->get('dateTo')
            ->shouldBeCalled()
            ->willReturn($dateToValue);

        $handlerRequest = new HandlerRequest($clientMock->reveal(), $dateFromValue, $dateToValue);

        $factory = new HandlerRequestFactory($this->requestStackMock->reveal(), $this->myselfMock->reveal());
        $result = $factory->__invoke();

        $this->assertInstanceOf(HandlerRequest::class, $result);
        $this->assertEquals($handlerRequest, $result);
    }
}
