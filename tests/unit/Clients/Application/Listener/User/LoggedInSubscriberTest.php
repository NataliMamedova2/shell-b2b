<?php

namespace Tests\Unit\Clients\Application\Listener\User;

use App\Clients\Application\Listener\User\LoggedInSubscriber;
use App\Clients\Domain\User\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Tests\Unit\Clients\Domain\User\UserTest;

final class LoggedInSubscriberTest extends TestCase
{
    /**
     * @var UserRepository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $repositoryMock;
    /**
     * @var ObjectManager|\Prophecy\Prophecy\ObjectProphecy
     */
    private $objectManagerMock;
    /**
     * @var LoggedInSubscriber
     */
    private $subscriber;
    /**
     * @var InteractiveLoginEvent
     */
    private $interactiveLoginEvent;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|TokenInterface
     */
    private $tokenInterfaceMock;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->prophesize(UserRepository::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);
        $this->tokenInterfaceMock = $this->prophesize(TokenInterface::class);

        $requestMock = $this->prophesize(Request::class);
        $this->interactiveLoginEvent = new InteractiveLoginEvent($requestMock->reveal(), $this->tokenInterfaceMock->reveal());

        $this->subscriber = new LoggedInSubscriber($this->repositoryMock->reveal(), $this->objectManagerMock->reveal());
    }

    public function testLoggedInUserNotInstanceOfUser(): void
    {
        $user = $this->prophesize(UserInterface::class);
        $this->tokenInterfaceMock->getUser()
            ->shouldBeCalled()
            ->willReturn($user);

        $this->repositoryMock->add($user)
            ->shouldNotBeCalled();
        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->subscriber->loggedIn($this->interactiveLoginEvent);
    }

    public function testLoggedIn(): void
    {
        $user = UserTest::createValidEntity();
        $this->tokenInterfaceMock->getUser()
            ->shouldBeCalled()
            ->willReturn($user);

        $user->loggedIn(new \DateTimeImmutable());
        $this->repositoryMock->add($user)
            ->shouldBeCalled();
        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $this->subscriber->loggedIn($this->interactiveLoginEvent);
    }
}

//class TestUser implements UserInterface
//{
//    /**
//     * Returns the roles granted to the user.
//     *
//     *     public function getRoles()
//     *     {
//     *         return ['ROLE_USER'];
//     *     }
//     *
//     * Alternatively, the roles might be stored on a ``roles`` property,
//     * and populated in any number of different ways when the user object
//     * is created.
//     *
//     * @return (Role|string)[] The user roles
//     */
//    public function getRoles()
//    {
//        // TODO: Implement getRoles() method.
//    }
//
//    /**
//     * Returns the password used to authenticate the user.
//     *
//     * This should be the encoded password. On authentication, a plain-text
//     * password will be salted, encoded, and then compared to this value.
//     *
//     * @return string|null The encoded password if any
//     */
//    public function getPassword()
//    {
//        // TODO: Implement getPassword() method.
//    }
//
//    /**
//     * Returns the salt that was originally used to encode the password.
//     *
//     * This can return null if the password was not encoded using a salt.
//     *
//     * @return string|null The salt
//     */
//    public function getSalt()
//    {
//        // TODO: Implement getSalt() method.
//    }
//
//    /**
//     * Returns the username used to authenticate the user.
//     *
//     * @return string The username
//     */
//    public function getUsername()
//    {
//        return '';
//    }
//
//    /**
//     * Removes sensitive data from the user.
//     *
//     * This is important if, at any given point, sensitive information like
//     * the plain-text password is stored on this object.
//     */
//    public function eraseCredentials()
//    {
//        // TODO: Implement eraseCredentials() method.
//    }
//}
