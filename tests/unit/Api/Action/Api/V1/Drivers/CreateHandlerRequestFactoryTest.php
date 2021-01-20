<?php

namespace Tests\Unit\Api\Action\Api\V1\Drivers;

use App\Api\Action\Api\V1\Drivers\CreateHandlerRequestFactory;
use App\Clients\Domain\Driver\UseCase\Create\HandlerRequest;
use App\Security\Cabinet\MyselfInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Tests\Unit\Clients\Domain\Client\ClientTest;

final class CreateHandlerRequestFactoryTest extends TestCase
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
     * @var ObjectProphecy|DenormalizerInterface
     */
    private $denormalizerMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->requestMock = $this->prophesize(Request::class);
        $this->denormalizerMock = $this->prophesize(DenormalizerInterface::class);
    }

    public function testConstructorNoRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new CreateHandlerRequestFactory($this->requestStackMock->reveal(), $this->myselfMock->reveal(), $this->denormalizerMock->reveal());
    }

    public function testInvokeRequestReturnHandlerRequest(): void
    {
        $data = [
            'firstName' => 'username',
            'lastName' => 'lastName',
            'middleName' => 'middleName',
            'email' => 'john.smith@example.com',
            'phones' => [
                'number' => '+23456789087'
            ],
            'carsNumbers' => [
                'number' => 'AA23355'
            ],
            'status' => 'blocked',
            'note' => 'note text',
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
        $handlerRequest->firstName = $data['firstName'];
        $handlerRequest->lastName = $data['lastName'];
        $handlerRequest->middleName = $data['middleName'];
        $handlerRequest->email = $data['email'];
        $handlerRequest->phones = $data['phones'];
        $handlerRequest->carsNumbers = $data['carsNumbers'];
        $handlerRequest->status = $data['status'];
        $handlerRequest->note = $data['note'];

        $this->denormalizerMock->denormalize($data, HandlerRequest::class)
            ->shouldBeCalled()
            ->willReturn($handlerRequest);

        $factory = new CreateHandlerRequestFactory($this->requestStackMock->reveal(), $this->myselfMock->reveal(), $this->denormalizerMock->reveal());

        $client = ClientTest::createValidEntity();
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $handlerRequest->client = $client;

        $result = $factory->__invoke();

        $this->assertInstanceOf(HandlerRequest::class, $result);

        $this->assertEquals($handlerRequest, $result);
    }
}
