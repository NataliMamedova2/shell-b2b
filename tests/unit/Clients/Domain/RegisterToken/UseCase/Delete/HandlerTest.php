<?php

namespace Tests\Unit\Clients\Domain\RegisterToken\UseCase\Delete;

use PHPUnit\Framework\TestCase;
use App\Clients\Domain\RegisterToken\Repository\RegisterRepository;
use App\Clients\Domain\RegisterToken\UseCase\Delete\Handler;
use App\Clients\Domain\RegisterToken\UseCase\Delete\HandlerRequest;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Tests\Unit\Clients\Domain\RegisterToken\RegisterTest;

final class HandlerTest extends TestCase
{
    /**
     * @var RegisterRepository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $repositoryMock;
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
        $this->repositoryMock = $this->prophesize(RegisterRepository::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);

        $this->handler = new Handler($this->repositoryMock->reveal(), $this->objectManagerMock->reveal());
    }

    public function testHandleEntityNotFoundReturnException(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';

        $handlerRequest = new HandlerRequest();
        $handlerRequest->setId($string);

        $entity = null;
        $this->repositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->expectException(EntityNotFoundException::class);

        $this->handler->handle($handlerRequest);
    }

    public function testHandleDeleteReturnEntity(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';

        $handlerRequest = new HandlerRequest();
        $handlerRequest->setId($string);

        $entity = RegisterTest::validEntity();
        $this->repositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->repositoryMock->remove($entity)
            ->shouldBeCalled();
        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEquals($entity->getId(), $result->getId());
    }
}
