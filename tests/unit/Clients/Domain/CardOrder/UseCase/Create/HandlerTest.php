<?php

namespace Tests\Unit\Clients\Domain\CardOrder\UseCase\Create;

use App\Clients\Domain\CardOrder\UseCase\Create\Handler;
use App\Clients\Domain\CardOrder\UseCase\Create\HandlerRequest;
use Doctrine\Common\Persistence\ObjectManager;
use Infrastructure\Interfaces\Repository\Repository;
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
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->prophesize(Repository::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);

        $this->handler = new Handler($this->repositoryMock->reveal(), $this->objectManagerMock->reveal());
    }

    public function testHandleReturnOrder(): void
    {
        $handlerRequest = new HandlerRequest();
        $handlerRequest->name = 'test name';
        $handlerRequest->count = 2;
        $handlerRequest->phone = '+3809876555';

        $handlerRequest->user = UserTest::createValidEntity();

        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEquals($handlerRequest->user, $result->getUser());
        $this->assertEquals($handlerRequest->name, $result->getName());
        $this->assertEquals($handlerRequest->count, $result->getCount());
        $this->assertEquals($handlerRequest->phone, $result->getPhone());
    }
}
