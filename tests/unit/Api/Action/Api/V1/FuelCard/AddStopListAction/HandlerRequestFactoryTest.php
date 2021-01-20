<?php

namespace Tests\Unit\Api\Action\Api\V1\FuelCard\AddStopListAction;

use App\Api\Action\Api\V1\FuelCard\AddStopListAction\HandlerRequestFactory;
use App\Clients\Domain\Card\UseCase\AddStopList\HandlerRequest;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Unit\Clients\Domain\Client\ClientTest;
use Tests\Unit\Clients\Domain\Card\CardTest;

final class HandlerRequestFactoryTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|RequestStack
     */
    private $requestStackMock;
    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $cardRepositoryMock;
    /**
     * @var MyselfInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $myselfMock;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|Request
     */
    private $requestMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->cardRepositoryMock = $this->prophesize(Repository::class);
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->requestMock = $this->prophesize(Request::class);
    }

    public function testInvokeNoRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new HandlerRequestFactory($this->requestStackMock->reveal(), $this->cardRepositoryMock->reveal(), $this->myselfMock->reveal());
    }

    public function testInvokeCardNotFoundReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $parameterBagMock = $this->prophesize(ParameterBag::class);
        $this->requestMock->attributes = $parameterBagMock;

        $id = '0559c42a-0957-42b8-9a23-5ba540273a17';
        $parameterBagMock
            ->get('id')
            ->shouldBeCalled()
            ->willReturn($id);

        $client = ClientTest::createValidEntity();
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $this->cardRepositoryMock->find(
            [
                'id_equalTo' => $id,
                'client1CId_equalTo' => $client->getClient1CId(),
            ]
        )
            ->shouldBeCalled()
            ->willReturn(null);

        $factory = new HandlerRequestFactory($this->requestStackMock->reveal(), $this->cardRepositoryMock->reveal(), $this->myselfMock->reveal());

        $this->expectException(NotFoundHttpException::class);
        $factory->__invoke();
    }

    public function testInvokeRequestReturnHandlerRequest(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($this->requestMock);

        $parameterBagMock = $this->prophesize(ParameterBag::class);
        $this->requestMock->attributes = $parameterBagMock;

        $id = '0559c42a-0957-42b8-9a23-5ba540273a17';
        $parameterBagMock
            ->get('id')
            ->shouldBeCalled()
            ->willReturn($id);

        $client = ClientTest::createValidEntity();
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $card = CardTest::createValidEntity();
        $this->cardRepositoryMock->find(
            [
                'id_equalTo' => $id,
                'client1CId_equalTo' => $client->getClient1CId(),
            ]
        )
            ->shouldBeCalled()
            ->willReturn($card);

        $factory = new HandlerRequestFactory($this->requestStackMock->reveal(), $this->cardRepositoryMock->reveal(), $this->myselfMock->reveal());

        $handlerRequest = new HandlerRequest();
        $handlerRequest->card = $card;

        $result = $factory->__invoke();

        $this->assertInstanceOf(HandlerRequest::class, $result);

        $this->assertEquals($handlerRequest, $result);
        $this->assertEquals($card, $result->card);
    }

}
