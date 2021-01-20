<?php

namespace Tests\Unit\Api\Action\Api\V1\FuelCard\CardOrder;

use App\Api\Action\Api\V1\FuelCard\CardOrder\HandlerRequestFactory;
use App\Clients\Domain\CardOrder\UseCase\Create\HandlerRequest;
use App\Security\Cabinet\MyselfInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Tests\Unit\Clients\Domain\User\UserTest;

final class HandlerRequestFactoryTest extends TestCase
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
     * @var \Prophecy\Prophecy\ObjectProphecy|Request
     */
    private $requestMock;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|DenormalizerInterface
     */
    private $denormalizerMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->requestMock = $this->prophesize(Request::class);
        $this->denormalizerMock = $this->prophesize(DenormalizerInterface::class);
    }

    public function testInvokeNoRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new HandlerRequestFactory($this->requestStackMock->reveal(), $this->myselfMock->reveal(), $this->denormalizerMock->reveal());
    }

    public function testInvokeRequestReturnHandlerRequest(): void
    {
        $data = [
            'count' => 3,
            'name' => 'username',
            'phone' => '+23456789087',
        ];

        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $parameterBagMock = $this->prophesize(ParameterBag::class);
        $this->requestMock->request = $parameterBagMock;

        $parameterBagMock
            ->all()
            ->shouldBeCalled()
            ->willReturn($data);

        $handlerRequest = new HandlerRequest();
        $handlerRequest->count = $data['count'];
        $handlerRequest->name = $data['name'];
        $handlerRequest->phone = $data['phone'];

        $this->denormalizerMock->denormalize($data, HandlerRequest::class)
            ->shouldBeCalled()
            ->willReturn($handlerRequest);

        $factory = new HandlerRequestFactory($this->requestStackMock->reveal(), $this->myselfMock->reveal(), $this->denormalizerMock->reveal());

        $user = UserTest::createValidEntity();
        $this->myselfMock->get()
            ->shouldBeCalled()
            ->willReturn($user);

        $handlerRequest->user = $user;

        $result = $factory->__invoke();

        $this->assertInstanceOf(HandlerRequest::class, $result);

        $this->assertEquals($handlerRequest, $result);
        $this->assertEquals($user, $result->user);
    }
}
