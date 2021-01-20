<?php

namespace Tests\Unit\Feedback\Domain\Feedback\UseCase\Create;

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\User;
use App\Feedback\Domain\Feedback\UseCase\Create\Handler;
use App\Feedback\Domain\Feedback\UseCase\Create\HandlerRequest;
use App\Feedback\Domain\Feedback\ValueObject\Comment;
use App\Feedback\Domain\Feedback\ValueObject\FeedbackCategory;
use App\Feedback\Domain\Feedback\ValueObject\FullName;
use Doctrine\Common\Persistence\ObjectManager;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\User\UserTest;

final class HandlerTest extends TestCase
{
    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $userRepositoryMock;

    /**
     * @var ObjectManager|\Prophecy\Prophecy\ObjectProphecy
     */
    private $entityManagerMock;

    /**
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->prophesize(Repository::class);
        $this->entityManagerMock = $this->prophesize(ObjectManager::class);

        $this->handler = new Handler(
            $this->userRepositoryMock->reveal(),
            $this->entityManagerMock->reveal()
        );
    }

    public function testHandleFeedbackCreateReturnEntity()
    {
        $handlerRequest = $this->getHandleRequest();

        $entity = $this->handler->handle($handlerRequest);

        $this->assertEquals($handlerRequest->email, $entity->getEmail());
        $this->assertEquals($handlerRequest->name, $entity->getName());
    }

    private function getHandleRequest(): HandlerRequest
    {
        $handlerRequest = new HandlerRequest();

        $handlerRequest->name = new FullName('Test name');
        $handlerRequest->email = new Email('test@mail.com');
        $handlerRequest->user = UserTest::createValidEntity();
        $handlerRequest->category = new FeedbackCategory('general-question');
        $handlerRequest->comment = new Comment('test comment text');

        return $handlerRequest;
    }
}
