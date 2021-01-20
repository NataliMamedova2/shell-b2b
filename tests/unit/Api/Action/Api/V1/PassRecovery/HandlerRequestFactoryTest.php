<?php

namespace Tests\Unit\Api\Action\Api\V1\PassRecovery;

use App\Api\Action\Api\V1\PasswordRecovery\HandlerRequestFactory;
use App\Clients\Domain\User\UseCase\ForgotPass\HandlerRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class HandlerRequestFactoryTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|RequestStack
     */
    private $requestStackMock;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|DenormalizerInterface
     */
    private $denormalizerMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->denormalizerMock = $this->prophesize(DenormalizerInterface::class);
    }

    public function testInvokeNoRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new HandlerRequestFactory($this->requestStackMock->reveal(), $this->denormalizerMock->reveal());
    }

    public function testInvokeRequestReturnHandlerRequest(): void
    {
        $data = [
            'username' => 'username',
        ];
        $request = Request::create('/', 'POST', $data);

        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($request);
        $handlerRequest = new HandlerRequest();
        $handlerRequest->username = $data['username'];

        $this->denormalizerMock->denormalize($data, HandlerRequest::class)
        ->shouldBeCalled()
        ->willReturn($handlerRequest);

        $factory = new HandlerRequestFactory($this->requestStackMock->reveal(), $this->denormalizerMock->reveal());

        $result = $factory->__invoke();

        $this->assertInstanceOf(HandlerRequest::class, $result);

        $this->assertEquals($handlerRequest, $result);
    }
}
