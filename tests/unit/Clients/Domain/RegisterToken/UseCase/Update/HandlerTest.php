<?php

namespace Tests\Unit\Clients\Domain\RegisterToken\UseCase\Update;

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\RegisterToken\Repository\RegisterRepository;
use App\Clients\Domain\RegisterToken\Service\TokenGenerator;
use App\Clients\Domain\RegisterToken\UseCase\Update\Handler;
use App\Clients\Domain\RegisterToken\UseCase\Update\HandlerRequest;
use App\Clients\Domain\RegisterToken\ValueObject\Token;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\RegisterToken\RegisterTest;

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
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->prophesize(RegisterRepository::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);
        $this->tokenGeneratorMock = $this->prophesize(TokenGenerator::class);

        $this->handler = new Handler(
            $this->repositoryMock->reveal(),
            $this->tokenGeneratorMock->reveal(),
            $this->objectManagerMock->reveal()
        );
    }

    public function testHandleEntityNotFoundReturnException(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';

        $handlerRequest = $this->getHandleRequest();
        $handlerRequest->setId($string);

        $entity = null;
        $this->repositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->tokenGeneratorMock->generate()
            ->shouldNotBeCalled();
        $this->repositoryMock->add($entity)
            ->shouldNotBeCalled();
        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->expectException(EntityNotFoundException::class);

        $this->handler->handle($handlerRequest);
    }

    private function getHandleRequest(): HandlerRequest
    {
        $handlerRequest = new HandlerRequest();

        $handlerRequest->email = 'email@email.com';

        return $handlerRequest;
    }

    public function testHandleUpdateReturnEntity(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';

        $handlerRequest = $this->getHandleRequest();
        $handlerRequest->setId($string);

        $entity = RegisterTest::validEntity();
        $this->repositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entity);

        $token = 'securetoken';
        $this->tokenGeneratorMock->generate()
            ->shouldBeCalled()
            ->willReturn($token);

        $entity->update(
            new Email($handlerRequest->email),
            new Token($token)
        );

        $this->repositoryMock->add($entity)
            ->shouldBeCalled();

        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEquals($entity->getToken()->getToken(), $result->getToken()->getToken());
    }
}
