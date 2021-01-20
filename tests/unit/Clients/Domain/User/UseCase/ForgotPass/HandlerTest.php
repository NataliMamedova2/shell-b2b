<?php

namespace Tests\Unit\Clients\Domain\User\UseCase\ForgotPass;

use App\Clients\Domain\User\Service\TokenGenerator;
use App\Clients\Domain\User\UseCase\ForgotPass\Handler;
use App\Clients\Domain\User\UseCase\ForgotPass\HandlerRequest;
use App\Clients\Domain\User\ValueObject\Token;
use App\Clients\Infrastructure\User\Criteria\Login;
use App\Mailer\Template;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Infrastructure\Interfaces\Repository\Repository;
use MailerBundle\Interfaces\Sender;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\User\UserTest;

final class HandlerTest extends TestCase
{
    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $repositoryMock;
    /**
     * @var ObjectManager|\Prophecy\Prophecy\ObjectProphecy
     */
    private $objectManagerMock;
    /**
     * @var TokenGenerator|\Prophecy\Prophecy\ObjectProphecy
     */
    private $tokenGeneratorMock;

    /**
     * @var Sender|\Prophecy\Prophecy\ObjectProphecy
     */
    private $senderMock;

    /**
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->prophesize(Repository::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);
        $this->tokenGeneratorMock = $this->prophesize(TokenGenerator::class);
        $this->senderMock = $this->prophesize(Sender::class);

        $this->handler = new Handler(
            $this->repositoryMock->reveal(),
            $this->objectManagerMock->reveal(),
            $this->tokenGeneratorMock->reveal(),
            $this->senderMock->reveal()
        );
    }

    public function testHandleUserNotFoundReturnException(): void
    {
        $loginOrEmail = 'test';

        $handlerRequest = new HandlerRequest();
        $handlerRequest->username = $loginOrEmail;

        $entity = null;
        $this->repositoryMock->find([
            Login::class => $handlerRequest->username,
        ])
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->tokenGeneratorMock->generate()
            ->shouldNotBeCalled();

        $this->repositoryMock->add($entity)
            ->shouldNotBeCalled();
        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->senderMock->send(null, Template::FORGOT_PASS, [
            'token' => null,
        ])
            ->shouldNotBeCalled();

        $this->expectException(EntityNotFoundException::class);

        $this->handler->handle($handlerRequest);
    }

    public function testHandleEntityFoundReturnEntity()
    {
        $loginOrEmail = 'test';
        $entity = UserTest::createValidEntity();
        $this->repositoryMock->find([
            Login::class => $loginOrEmail,
        ])
            ->shouldBeCalled()
            ->willReturn($entity);

        $testToken = 'test_token';
        $this->tokenGeneratorMock->generate()
            ->shouldBeCalled()
            ->willReturn($testToken);

        $entity->setRestoreToken(new Token($testToken));

        $this->repositoryMock->add($entity)
            ->shouldBeCalled();

        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $this->senderMock->send($entity->getEmail(), Template::FORGOT_PASS, [
            'token' => $testToken,
        ])->shouldBeCalled();

        $handlerRequest = new HandlerRequest();
        $handlerRequest->username = $loginOrEmail;

        $updatedEntity = $this->handler->handle($handlerRequest);

        $this->assertEquals($testToken, $updatedEntity->getRestoreToken()->getToken());
    }
}
