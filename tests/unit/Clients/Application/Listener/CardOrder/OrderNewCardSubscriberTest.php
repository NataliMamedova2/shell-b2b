<?php

namespace Tests\Unit\Clients\Application\Listener\CardOrder;

use App\Clients\Application\Listener\CardOrder\OrderNewCardSubscriber;
use App\Clients\Domain\CardOrder\Order;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\User\User;
use App\Mailer\Template;
use App\Users\Domain\User\Repository\UserRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use MailerBundle\Interfaces\Sender;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\CardOrder\OrderTest;
use Tests\Unit\Users\Domain\User\UserTest;

final class OrderNewCardSubscriberTest extends TestCase
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
     * @var OrderNewCardSubscriber
     */
    private $subscriber;
    /**
     * @var LifecycleEventArgs|\Prophecy\Prophecy\ObjectProphecy
     */
    private $lifecycleArgsMock;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->prophesize(UserRepository::class);
        $this->senderMock = $this->prophesize(Sender::class);

        $this->lifecycleArgsMock = $this->prophesize(LifecycleEventArgs::class);

        $this->subscriber = new OrderNewCardSubscriber($this->userRepositoryMock->reveal(), $this->senderMock->reveal());
    }

    public function testPostPersistNullEntity()
    {
        $this->lifecycleArgsMock->getObject()
            ->shouldBeCalled()
            ->willReturn(null);

        $this->subscriber->postPersist($this->lifecycleArgsMock->reveal());
    }

    public function testPostPersistManagerNotFoundReturn()
    {
        $entityMock = $this->prophesize(Order::class);
        $this->lifecycleArgsMock->getObject()
            ->shouldBeCalled()
            ->willReturn($entityMock->reveal());

        $userMock = $this->prophesize(User::class);
        $entityMock->getUser()
            ->shouldBeCalled()
            ->willReturn($userMock->reveal());

        $companyMock = $this->prophesize(Company::class);
        $userMock->getCompany()
            ->shouldBeCalled()
            ->willReturn($companyMock->reveal());

        $clientMock = $this->prophesize(Client::class);
        $companyMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock->reveal());

        $managerId = 'MID09876';
        $clientMock->getManager1CId()
            ->shouldBeCalled()
            ->willReturn($managerId);

        $this->userRepositoryMock->find([
            'manager1CId_equalTo' => $managerId,
        ])
            ->shouldBeCalled()
            ->willReturn(null);

        $this->subscriber->postPersist($this->lifecycleArgsMock->reveal());
    }

    public function testPostPersistSendMail()
    {
        $entity = OrderTest::createValidEntity();
        $this->lifecycleArgsMock->getObject()
            ->shouldBeCalled()
            ->willReturn($entity);

        $user = $entity->getUser();
        $company = $user->getCompany();

        $managerId = $company->getClient()->getManager1CId();

        $user = UserTest::createValidEntity();
        $this->userRepositoryMock->find([
            'manager1CId_equalTo' => $managerId,
        ])
            ->shouldBeCalled()
            ->willReturn($user);

        $this->senderMock->send($user->getEmail(), Template::ORDER_NEW_CARD, [
            'client' => $company->getClient(),
            'name' => $entity->getName(),
            'count' => $entity->getCount(),
            'phone' => $entity->getPhone(),
        ]);

        $this->subscriber->postPersist($this->lifecycleArgsMock->reveal());
    }
}
