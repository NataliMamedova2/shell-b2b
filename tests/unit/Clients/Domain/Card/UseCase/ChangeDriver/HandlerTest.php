<?php

namespace Tests\Unit\Clients\Domain\Card\UseCase\ChangeDriver;

use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\UseCase\ChangeDriver\Handler;
use App\Clients\Domain\Card\UseCase\ChangeDriver\HandlerRequest;
use App\Clients\Domain\Card\ValueObject\CardId;
use App\Clients\Domain\Driver\Driver;
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
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->prophesize(Repository::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);

        $this->handler = new Handler($this->repositoryMock->reveal(), $this->objectManagerMock->reveal());
    }

    public function testHandleCardNotFoundReturnException(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $cardId = CardId::fromString($string);

        $driverMock = $this->prophesize(Driver::class);
        $handlerRequest = new HandlerRequest($cardId, $driverMock->reveal());

        $this->repositoryMock->findById($string)
            ->shouldBeCalled()
            ->willReturn(null);

        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->expectException(EntityNotFoundException::class);
        $this->handler->handle($handlerRequest);
    }

    public function testHandleCardFoundReturnCardEntity(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $cardId = CardId::fromString($string);

        $driverMock = $this->prophesize(Driver::class);
        $handlerRequest = new HandlerRequest($cardId, $driverMock->reveal());

        $cardMock = $this->prophesize(Card::class);
        $this->repositoryMock->findById($handlerRequest->getCardId()->getId())
            ->shouldBeCalled()
            ->willReturn($cardMock->reveal());

        $cardMock->changeDriver($handlerRequest->getDriver())
            ->shouldBeCalled();

        $this->repositoryMock->add($cardMock->reveal())
            ->shouldBeCalled();
        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEquals($cardMock->reveal(), $result);
    }
}
