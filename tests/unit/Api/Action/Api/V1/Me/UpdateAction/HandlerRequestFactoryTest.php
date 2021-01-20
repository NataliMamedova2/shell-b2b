<?php

namespace Tests\Unit\Api\Action\Api\V1\Me\UpdateAction;

use App\Api\Action\Api\V1\Me\UpdateAction\HandlerRequestFactory;
use App\Clients\Domain\User\UseCase\UpdateProfile\HandlerRequest;
use App\Security\Cabinet\Myself;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Tests\Unit\Clients\Domain\User\UserTest;

final class HandlerRequestFactoryTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|RequestStack
     */
    private $requestStackMock;
    /**
     * @var Myself
     */
    private $myself;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);

        $tokenStorage = new TokenStorage();
        $user = UserTest::createValidEntity();
        $token = new AnonymousToken('secret', $user);
        $tokenStorage->setToken($token);
        $this->myself = new Myself($tokenStorage);
    }

    public function testInvokeNoRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $factory = new HandlerRequestFactory($this->requestStackMock->reveal(), $this->myself);

        $this->expectException(\InvalidArgumentException::class);

        $factory->__invoke();
    }

    public function testInvokeRequestReturnHandlerRequest(): void
    {
        $parameters = [
            'username' => 'username',
        ];
        $request = Request::create('/', 'GET', $parameters);

        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($request);

        $factory = new HandlerRequestFactory($this->requestStackMock->reveal(), $this->myself);

        $handlerRequest = new HandlerRequest($this->myself->get());

        $result = $factory->__invoke();

        $this->assertInstanceOf(HandlerRequest::class, $result);

        $this->assertEquals($handlerRequest, $result);
    }
}
