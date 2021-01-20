<?php

namespace Tests\Unit\Clients\Domain\Driver\UseCase\Delete;

use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\UseCase\Delete\Handler;
use App\Clients\Domain\Driver\UseCase\Delete\HandlerRequest;
use App\Clients\Domain\Driver\ValueObject\DriverId;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class HandlerTest extends TestCase
{
    /**
     * @var Repository|ObjectProphecy
     */
    private $repositoryMock;
    /**
     * @var ObjectManager|ObjectProphecy
     */
    private $objectManagerMock;
    /**
     * @var \App\Clients\Domain\Driver\UseCase\Create\Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->prophesize(Repository::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);

        $this->handler = new Handler($this->repositoryMock->reveal(), $this->objectManagerMock->reveal());
    }

    public function testHandleEntityNotFoundReturnException()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $id = DriverId::fromString($string);

        $handlerRequest = new HandlerRequest($id);

        $this->repositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn(null);

        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->expectException(EntityNotFoundException::class);

        $this->handler->handle($handlerRequest);
    }

    public function testHandleEntityFoundRemoveEntity()
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $id = DriverId::fromString($string);

        $handlerRequest = new HandlerRequest($id);

        $entityMock = $this->prophesize(Driver::class);

        $this->repositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entityMock->reveal());

        $this->repositoryMock->remove($entityMock->reveal())
            ->shouldBeCalled();
        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $this->handler->handle($handlerRequest);
    }
}
