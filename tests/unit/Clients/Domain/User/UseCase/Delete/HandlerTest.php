<?php

namespace Tests\Unit\Clients\Domain\User\UseCase\Delete;

use PHPUnit\Framework\TestCase;
use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\UseCase\Delete\Handler;
use App\Clients\Domain\User\UseCase\Delete\HandlerRequest;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Tests\Unit\Clients\Domain\User\UserTest;

final class HandlerTest extends TestCase
{
    /**
     * @var UserRepository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $userRepositoryMock;
    /**
     * @var ObjectManager|\Prophecy\Prophecy\ObjectProphecy
     */
    private $objectManagerMock;
    /**
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->prophesize(UserRepository::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);

        $this->handler = new Handler($this->userRepositoryMock->reveal(), $this->objectManagerMock->reveal());
    }

    public function testHandleEntityNotFoundReturnException(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';

        $handlerRequest = new HandlerRequest();
        $handlerRequest->setId($string);

        $entity = null;
        $this->userRepositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->expectException(EntityNotFoundException::class);

        $this->handler->handle($handlerRequest);
    }

    public function testHandleEntityFoundReturnEntity(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';

        $handlerRequest = new HandlerRequest();
        $handlerRequest->setId($string);

        $entity = UserTest::createValidEntity();
        $this->userRepositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->userRepositoryMock->remove($entity)
            ->shouldBeCalled();
        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEquals($entity->getId(), $result->getId());
    }
}
