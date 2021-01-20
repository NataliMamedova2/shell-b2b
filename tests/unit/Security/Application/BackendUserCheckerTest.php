<?php

namespace Tests\Unit\Security\Application;

use App\Security\Application\BackendUserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserInterface;
use Tests\Unit\Users\Domain\User\UserTest;

final class BackendUserCheckerTest extends TestCase
{
    /**
     * @var BackendUserChecker
     */
    private $checker;

    protected function setUp(): void
    {
        $this->checker = new BackendUserChecker();
    }

    public function testCheckPreAuthIdentityNotInstanceofUser(): void
    {
        $identity = new BackendTestUser();

        $result = $this->checker->checkPreAuth($identity);

        $this->assertNull($result);
    }

    public function testCheckPreAuthUserIsNotActiveReturnException(): void
    {
        $this->expectException(DisabledException::class);

        $identity = UserTest::createValidEntity(['status' => 0]);

        $this->checker->checkPreAuth($identity);
    }

    public function testCheckPostAuthIdentityNotInstanceofUser(): void
    {
        $identity = new BackendTestUser();

        $result = $this->checker->checkPostAuth($identity);

        $this->assertNull($result);
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
