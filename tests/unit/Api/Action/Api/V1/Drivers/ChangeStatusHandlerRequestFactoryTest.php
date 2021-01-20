<?php

namespace Tests\Unit\Api\Action\Api\V1\Drivers;

use App\Api\Action\Api\V1\Drivers\ChangeStatusHandlerRequestFactory;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\UseCase\ChangeStatus\HandlerRequest;
use App\Clients\Domain\Driver\ValueObject\DriverId;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ChangeStatusHandlerRequestFactoryTest extends TestCase
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
     * @var Repository|ObjectProphecy
     */
    private $repositoryMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->repositoryMock = $this->prophesize(Repository::class);
        $this->requestMock = $this->prophesize(Request::class);
    }

    public function testConstructorNoRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new ChangeStatusHandlerRequestFactory(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal(),
            $this->repositoryMock->reveal()
        );
    }

    public function testInvokeDriverNotFoundReturnException(): void
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

        $this->repositoryMock->find([
            'id_equalTo' => $string,
            'client1CId_equalTo' => $client1CIdValue,
        ])
            ->shouldBeCalled()
            ->willReturn(null);

        $request = new ChangeStatusHandlerRequestFactory(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal(),
            $this->repositoryMock->reveal()
        );

        $this->expectException(NotFoundHttpException::class);

        $request->__invoke();
    }

    public function testInvokeDriverFoundReturnHandlerRequest(): void
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

        $driverMock = $this->prophesize(Driver::class);
        $this->repositoryMock->find([
            'id_equalTo' => $string,
            'client1CId_equalTo' => $client1CIdValue,
        ])
            ->shouldBeCalled()
            ->willReturn($driverMock);

        $statusValue = 'blocked';
        $this->requestMock->get('status')
            ->shouldBeCalled()
            ->willReturn($statusValue);

        $driverId = DriverId::fromString($string);
        $handlerRequestResult = new HandlerRequest($driverId);
        $handlerRequestResult->status = $statusValue;

        $factory = new ChangeStatusHandlerRequestFactory(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal(),
            $this->repositoryMock->reveal()
        );

        $result = $factory->__invoke();

        $this->assertInstanceOf(HandlerRequest::class, $result);

        $this->assertEquals($handlerRequestResult, $result);
    }
}
