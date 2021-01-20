<?php

namespace Tests\Unit\Security\Provider;

use App\Security\Provider\BackendUserProvider;
use App\Users\Domain\User\Repository\UserRepository;
use App\Users\Domain\User\User;
use App\Users\Infrastructure\Criteria\Login;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Tests\Unit\Users\Domain\User\UserTest;

class BackendUserProviderTest extends TestCase
{
    /**
     * @var UserRepository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $repositoryMock;

    /**
     * @var BackendUserProvider
     */
    private $provider;

    public function setUp(): void
    {
        $this->repositoryMock = $this->prophesize(UserRepository::class);

        $this->provider = new BackendUserProvider($this->repositoryMock->reveal());
    }

    // refreshUser

    public function testRefreshUserNotSupportedClassReturnException(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $user = new BackendTestUser();
        $this->provider->refreshUser($user);
    }

    public function testRefreshUserSupportedClassReturnUser(): void
    {
        $user = UserTest::createValidEntity();

        $this->repositoryMock->find([Login::class => $user->getEmail()])
            ->shouldBeCalled()
            ->willReturn($user);

        $this->provider->refreshUser($user);
    }

    public function testRefreshUserNotSupportedClassReturnUsernameNotFoundException(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $user = new BackendTestUser();

        $this->provider->refreshUser($user);

        $this->repositoryMock->find([Login::class => 'sdsds'])
            ->shouldBeCalled()
            ->willReturn(null);
    }

    // loadUserByUsername

    public function testLoadUserByUsernameReturnUser(): void
    {
        $user = UserTest::createValidEntity();

        $username = $user->getEmail();

        $this->repositoryMock->find([Login::class => $username])
            ->shouldBeCalled()
            ->willReturn($user);

        $this->provider->loadUserByUsername($username);
    }

    public function testLoadUserByUsernameReturnException(): void
    {
        $this->expectException(UsernameNotFoundException::class);

        $username = 'test';
        $this->provider->loadUserByUsername($username);

        $this->repositoryMock->find([Login::class => $username])
            ->shouldBeCalled()
            ->willReturn(null);
    }

    // supportsClass

    public function testSupportsClassClassSupportedReturnTrue(): void
    {
        $result = $this->provider->supportsClass(User::class);

        $this->assertEquals(true, $result);
    }

    public function testSupportsClassClassNotSupportedReturnFalse(): void
    {
        $result = $this->provider->supportsClass(BackendTestUser::class);

        $this->assertEquals(false, $result);
    }
}

class BackendTestUser implements UserInterface
{
    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return '';
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
