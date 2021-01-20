<?php

namespace Tests\Unit\Api\Action\Api\V1\Drivers;

use App\Api\Action\Api\V1\Drivers\UpdateHandlerRequestFactory;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\UseCase\Update\HandlerRequest;
use App\Clients\Domain\Driver\ValueObject\DriverId;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class UpdateHandlerRequestFactoryTest extends TestCase
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
        $this->denormalizerMock = $this->prophesize(DenormalizerInterface::class);
    }

    public function testConstructorNoRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new UpdateHandlerRequestFactory(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal(),
            $this->repositoryMock->reveal(),
            $this->denormalizerMock->reveal()
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

        $request = new UpdateHandlerRequestFactory(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal(),
            $this->repositoryMock->reveal(),
            $this->denormalizerMock->reveal()
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

        $parameterBagMock = $this->prophesize(ParameterBag::class);
        $this->requestMock->request = $parameterBagMock;

        $data = [
            'firstName' => 'firstName',
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
        $parameterBagMock
            ->all()
            ->shouldBeCalled()
            ->willReturn($data);

        $driverId = DriverId::fromString($string);
        $handlerRequest = new HandlerRequest($driverId);
        $context = [
            AbstractObjectNormalizer::OBJECT_TO_POPULATE => $handlerRequest,
        ];

        $handlerRequestResult = new HandlerRequest($driverId);
        $handlerRequestResult->firstName = $data['firstName'];
        $handlerRequestResult->lastName = $data['lastName'];
        $handlerRequestResult->middleName = $data['middleName'];
        $handlerRequestResult->email = $data['email'];
        $handlerRequestResult->phones = $data['phones'];
        $handlerRequestResult->carsNumbers = $data['carsNumbers'];
        $handlerRequestResult->status = $data['status'];
        $handlerRequestResult->note = $data['note'];

        $this->denormalizerMock->denormalize($data, HandlerRequest::class, null, $context)
            ->shouldBeCalled()
            ->willReturn($handlerRequestResult);

        $factory = new UpdateHandlerRequestFactory(
            $this->requestStackMock->reveal(),
            $this->myselfMock->reveal(),
            $this->repositoryMock->reveal(),
            $this->denormalizerMock->reveal()
        );

        $result = $factory->__invoke();

        $this->assertInstanceOf(HandlerRequest::class, $result);

        $this->assertEquals($handlerRequestResult, $result);
    }
}
