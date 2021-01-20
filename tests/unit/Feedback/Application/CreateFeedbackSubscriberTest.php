<?php

namespace Test\Unit\Feedback\Application\Listener;

use App\Application\Domain\ValueObject\Email;
use App\Feedback\Application\Listener\CreateFeedbackSubscriber;
use App\Feedback\Domain\Feedback\Feedback;
use App\Feedback\Domain\Feedback\ValueObject\Comment;
use App\Feedback\Domain\Feedback\ValueObject\FeedbackCategory;
use App\Feedback\Domain\Feedback\ValueObject\FeedbackId;
use App\Feedback\Domain\Feedback\ValueObject\FullName;
use App\Mailer\Template;
use App\Users\Domain\User\Repository\UserRepository;
use App\Users\Domain\User\ValueObject\Role;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use MailerBundle\Interfaces\Sender;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Users\Domain\User\UserTest;

final class CreateFeedbackSubscriberTest extends TestCase
{
    /**
     * @var UserRepository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $userRepositoryMock;
    /**
     * @var Sender|\Prophecy\Prophecy\ObjectProphecy
     */
    private $senderMock;
    /**
     * @var CreateFeedbackSubscriber
     */
    private $subscriber;
    /**
     * @var LifecycleEventArgs
     */
    private $lifecycleArgs;

    /**
     * @var Feedback
     */
    private $feedbackEntity;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->prophesize(UserRepository::class);
        $this->senderMock = $this->prophesize(Sender::class);

        $entityManagerMock = $this->prophesize(ObjectManager::class);

        $idString = '550e8400-e29b-41d4-a716-446655440000';
        $id = FeedbackId::fromString($idString);
        $name = new FullName('Test name');
        $email = new Email('test@mail.com');
        $user = \Tests\Unit\Clients\Domain\User\UserTest::createValidEntity();
        $category = new FeedbackCategory('general-question');
        $comment = new Comment('test comment text');

        $this->feedbackEntity = Feedback::create(
            $id,
            $email,
            $user,
            $name,
            $category,
            $comment
        );

        $this->lifecycleArgs = new LifecycleEventArgs($this->feedbackEntity, $entityManagerMock->reveal());

        $this->subscriber = new CreateFeedbackSubscriber($this->senderMock->reveal(), $this->userRepositoryMock->reveal());
    }

    public function testSendLettersSuccess()
    {
        $this->senderMock->send($this->feedbackEntity->getCategory()->getManagerEmail(), Template::FEEDBACK, [
            'feedback' => $this->feedbackEntity,
        ])
            ->shouldBeCalled();

        $this->senderMock->send("Denys.Vynohradskyi@shell.com", Template::FEEDBACK, [
            'feedback' => $this->feedbackEntity,
        ])
            ->shouldBeCalled();

        $this->subscriber->postPersist($this->lifecycleArgs);
    }
}
