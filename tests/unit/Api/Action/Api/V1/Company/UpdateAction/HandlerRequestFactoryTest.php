<?php

namespace Tests\Unit\Api\Action\Api\V1\Company\UpdateAction;

use App\Api\Action\Api\V1\Company\UpdateAction\HandlerRequestFactory;
use App\Clients\Domain\Company\UseCase\Update\HandlerRequest;
use App\Clients\Domain\User\User;
use App\Security\Cabinet\MyselfInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Tests\Unit\Clients\Domain\Company\CompanyTest;

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
     * @var ObjectProphecy|DenormalizerInterface
     */
    private $denormalizerMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->denormalizerMock = $this->prophesize(DenormalizerInterface::class);
    }

    public function testConstructorEmptyRequestReturnException(): void
    {
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new HandlerRequestFactory($this->requestStackMock->reveal(), $this->myselfMock->reveal(), $this->denormalizerMock->reveal());
    }

    public function testInvokeReturnHandlerRequest(): void
    {
        $parameters = [
            'name' => 'Auchan Ukraine',
            'accountingEmail' => 'example@mail.com',
            'accountingPhone' => '+380963332211',
            'postalAddress' => '01001 м. Київ',
        ];
        $request = Request::create('/', 'GET', $parameters);
        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($request);

        $data = $request->request->all();

        $handlerRequest = new HandlerRequest();
        $handlerRequest->name = $parameters['name'];
        $handlerRequest->accountingEmail = $parameters['accountingEmail'];
        $handlerRequest->accountingPhone = $parameters['accountingPhone'];
        $handlerRequest->postalAddress = $parameters['postalAddress'];
        $this->denormalizerMock->denormalize($data, HandlerRequest::class)
            ->shouldBeCalled()
            ->willReturn($handlerRequest);

        $userMock = $this->prophesize(User::class);
        $this->myselfMock->get()
            ->shouldBeCalled()
            ->willReturn($userMock);

        $company = CompanyTest::createValidEntity();
        $userMock->getCompany()
            ->shouldBeCalled()
            ->willReturn($company);

        $handlerRequest->setId($company->getId());

        $factory = new HandlerRequestFactory($this->requestStackMock->reveal(), $this->myselfMock->reveal(), $this->denormalizerMock->reveal());
        $result = $factory->__invoke();

        $this->assertInstanceOf(HandlerRequest::class, $result);
        $this->assertEquals($handlerRequest, $result);
    }
}
