<?php

namespace Tests\Unit\Clients\Domain\User\UseCase\ChangeStatus;

use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\UseCase\ChangeStatus\Handler;
use App\Clients\Domain\User\UseCase\ChangeStatus\HandlerRequest;
use App\Clients\Domain\User\ValueObject\Status;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
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

    public function testHandleEntityNotFoundReturnException()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';

        $handlerRequest = new HandlerRequest();
        $handlerRequest->setId($string);

        $entity = null;
        $this->userRepositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->userRepositoryMock->add($entity)
            ->shouldNotBeCalled();
        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->expectException(EntityNotFoundException::class);

        $this->handler->handle($handlerRequest);
    }

    public function testHandleEntityFoundReturnEntity()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';

        $handlerRequest = new HandlerRequest();
        $handlerRequest->setId($string);
        $handlerRequest->status = 'blocked';

        $entity = UserTest::createValidEntity();
        $this->userRepositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entity);

        $newStatus = Status::fromName($handlerRequest->status);
        $entity->changeStatus($newStatus);

        $this->userRepositoryMock->add($entity)
            ->shouldBeCalled();
        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEquals($entity->getId(), $result->getId());
        $this->assertEquals($entity->getStatus(), $result->getStatus());
    }
}
