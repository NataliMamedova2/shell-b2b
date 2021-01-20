<?php

namespace Tests\Unit\Api\Action\Api\V1\Feedback;

use App\Api\Action\Api\V1\Feedback\CreateAction;
use App\Api\Crud\Interfaces\Response;
use App\Feedback\Domain\Feedback\UseCase\Create\HandlerRequest;
use App\Security\Cabinet\Myself;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Interfaces\Handler;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Unit\Clients\Domain\User\UserTest;

class CreateActionTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|ValidatorInterface
     */
    private $validatorMock;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|SerializerInterface
     */
    private $serializerMock;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|Handler
     */
    private $handlerMock;
    /**
     * @var Myself
     */
    private $myself;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|Response
     */
    private $responseMock;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|Request
     */
    private $requestMock;

    /**
     * @var CreateAction
     */
    private $controller;

    protected function setUp(): void
    {
        $this->validatorMock = $this->prophesize(ValidatorInterface::class);
        $this->serializerMock = $this->prophesize(SerializerInterface::class);
        $this->handlerMock = $this->prophesize(Handler::class);
        $this->responseMock = $this->prophesize(Response::class);
        $this->requestMock = $this->prophesize(Request::class);

        $userRepositoryMock = $this->prophesize(Repository::class);
        $entityManagerMock = $this->prophesize(ObjectManager::class);

        $handler = new \App\Feedback\Domain\Feedback\UseCase\Create\Handler(
            $userRepositoryMock->reveal(),
            $entityManagerMock->reveal()
        );

        $tokenStorage = new TokenStorage();
        $user = UserTest::createValidEntity();
        $token = new AnonymousToken('secret', $user);
        $tokenStorage->setToken($token);
        $this->myself = new Myself($tokenStorage);

        $this->controller = new CreateAction(
            $this->validatorMock->reveal(),
            $this->serializerMock->reveal(),
            $handler,
            $this->myself,
            $this->responseMock->reveal()
        );
    }

    public function testNotValidDataReturnErrorResponse(): void
    {
        $this->requestMock->getContent()
            ->shouldBeCalled()
            ->willReturn(null);

        $errors = [
            "No data found"
        ];

        $this->responseMock->createErrorResponse($errors)
            ->shouldBeCalled()
            ->willReturn(\Symfony\Component\HttpFoundation\Response::create());

        $result = $this->controller->__invoke($this->requestMock->reveal());

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $result);
    }

    public function testValidDataReturnSuccessResponse(): void
    {
        $data = [
            'category' => 'general-question',
            'name' => 'user name',
            'email' => 'user@name.com',
            'comment' => 'test',
        ];
        $this->requestMock->getContent()
            ->shouldBeCalled()
            ->willReturn($data);

        $handlerRequest = new HandlerRequest();
        $handlerRequest->email = $data['email'];
        $handlerRequest->category = $data['category'];
        $handlerRequest->name = $data['name'];
        $handlerRequest->comment = $data['comment'];

        $this->serializerMock->deserialize($data, HandlerRequest::class, 'json')
            ->shouldBeCalled()
            ->willReturn($handlerRequest);

        $handlerRequest->user = $this->myself->get();
        $this->validatorMock->validate($handlerRequest)
            ->shouldBeCalled()
            ->willReturn([]);

        $data = [
            'success' => true,
        ];

        $response = \Symfony\Component\HttpFoundation\Response::create(json_encode($data));
        $this->responseMock->createSuccessResponse($data)
            ->shouldBeCalled()
            ->willReturn($response);

        $result = $this->controller->__invoke($this->requestMock->reveal());

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $result);
        $this->assertEquals(json_encode($data), $response->getContent());
    }
}
