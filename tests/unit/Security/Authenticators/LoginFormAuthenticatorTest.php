<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security\Authenticators;

use App\Security\Authenticators\LoginFormAuthenticator;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginFormAuthenticatorTest extends TestCase
{
    /**
     * @var ObjectProphecy|UrlGeneratorInterface
     */
    private $urlGeneratorMock;
    /**
     * @var ObjectProphecy|CsrfTokenManagerInterface
     */
    private $csrfTokenManager;
    /**
     * @var ObjectProphecy|UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var ObjectProphecy|Request
     */
    private $request;
    /**
     * @var ObjectProphecy|ParameterBag
     */
    private $parameterBag;
    /**
     * @var ObjectProphecy|SessionInterface
     */
    private $session;
    /**
     * @var LoginFormAuthenticator
     */
    private $loginFormAuthenticator;
    /**
     * @var ObjectProphecy|UserProviderInterface
     */
    private $userProvider;
    /**
     * @var ObjectProphecy|TokenInterface
     */
    private $tokenInterface;

    public function setUp(): void
    {
        $this->urlGeneratorMock = $this->prophesize(UrlGeneratorInterface::class);
        $this->csrfTokenManager = $this->prophesize(CsrfTokenManagerInterface::class);
        $this->passwordEncoder = $this->prophesize(UserPasswordEncoderInterface::class);
        $this->request = $this->prophesize(Request::class);
        $this->session = $this->prophesize(SessionInterface::class);
        $this->parameterBag = $this->prophesize(ParameterBag::class);
        $this->userProvider = $this->prophesize(UserProviderInterface::class);
        $this->tokenInterface = $this->prophesize(TokenInterface::class);

        $this->loginFormAuthenticator = new LoginFormAuthenticator($this->urlGeneratorMock->reveal(), $this->csrfTokenManager->reveal(), $this->passwordEncoder->reveal());
    }

    // supports

    /**
     * @test
     */
    public function testSupportsRouteNotSignInReturnFalse(): void
    {
        $route = 'test_route';
        $this->request->attributes = new ParameterBag(['_route' => $route]);

        $this->request->isMethod('POST')
            ->shouldNotBeCalled();

        $result = $this->loginFormAuthenticator->supports($this->request->reveal());

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function testSupportsRouteSignInReturnFalse(): void
    {
        $route = 'admin_sign_in';
        $this->request->attributes = new ParameterBag(['_route' => $route]);

        $this->request->isMethod('POST')
            ->shouldBeCalled()
            ->willReturn(false);

        $result = $this->loginFormAuthenticator->supports($this->request->reveal());

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function testSupportsRouteSignInReturnTrue(): void
    {
        $route = 'admin_sign_in';
        $this->request->attributes = new ParameterBag(['_route' => $route]);

        $this->request->isMethod('POST')
            ->shouldBeCalled()
            ->willReturn(true);

        $result = $this->loginFormAuthenticator->supports($this->request->reveal());

        $this->assertTrue($result);
    }

    // getCredentials

    /**
     * @test
     */
    public function testGetCredentialsReturnArray()
    {
        $username = 'username';
        $password = 'password';
        $csrfToken = 'csrf_token';

        $this->request->request = new ParameterBag([
            'username' => $username,
            'password' => $password,
            '_csrf_token' => $csrfToken,
        ]);

        $this->request->getSession()
            ->shouldBeCalled()
            ->willReturn($this->session);

        $this->session->set(Security::LAST_USERNAME, $username);

        $result = $this->loginFormAuthenticator->getCredentials($this->request->reveal());

        $this->assertEquals([
            'username' => $username,
            'password' => $password,
            'csrf_token' => $csrfToken,
        ], $result);

        return $result;
    }

    // getUser

    /**
     * @test
     *
     * @depends testGetCredentialsReturnArray
     *
     * @param array $credentials
     */
    public function testGetUserReturnTokenException(array $credentials): void
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        $this->csrfTokenManager->isTokenValid($token)
            ->shouldBeCalled()
            ->willReturn(false);

        $this->expectException(InvalidCsrfTokenException::class);

        $this->userProvider->loadUserByUsername($credentials['username'])
            ->shouldNotBeCalled();

        $this->loginFormAuthenticator->getUser($credentials, $this->userProvider->reveal());
    }

    /**
     * @test
     *
     * @depends testGetCredentialsReturnArray
     *
     * @param array $credentials
     */
    public function testGetUserReturnCustomUserMessageException(array $credentials): void
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        $this->csrfTokenManager->isTokenValid($token)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->userProvider->loadUserByUsername($credentials['username'])
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(CustomUserMessageAuthenticationException::class);

        $this->loginFormAuthenticator->getUser($credentials, $this->userProvider->reveal());
    }

    /**
     * @test
     *
     * @depends testGetCredentialsReturnArray
     *
     * @param array $credentials
     *
     * @return User
     */
    public function testGetUserReturnUser(array $credentials)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        $this->csrfTokenManager->isTokenValid($token)
            ->shouldBeCalled()
            ->willReturn(true);

        $user = new User($credentials['username'], 'password_hash');

        $this->userProvider->loadUserByUsername($credentials['username'])
            ->shouldBeCalled()
            ->willReturn($user);

        $this->loginFormAuthenticator->getUser($credentials, $this->userProvider->reveal());

        return $user;
    }

    // checkCredentials

    /**
     * @test
     *
     * @depends testGetCredentialsReturnArray
     * @depends testGetUserReturnUser
     *
     * @param array $credentials
     * @param $user
     */
    public function testCheckCredentialsFalse(array $credentials, $user): void
    {
        $this->passwordEncoder->isPasswordValid($user, $credentials['password'])
            ->shouldBeCalled()
            ->willReturn(false);

        $result = $this->loginFormAuthenticator->checkCredentials($credentials, $user);

        $this->assertFalse($result);
    }

    /**
     * @test
     *
     * @depends testGetCredentialsReturnArray
     * @depends testGetUserReturnUser
     *
     * @param array $credentials
     * @param $user
     */
    public function testCheckCredentialsTrue(array $credentials, $user): void
    {
        $this->passwordEncoder->isPasswordValid($user, $credentials['password'])
            ->shouldBeCalled()
            ->willReturn(true);

        $result = $this->loginFormAuthenticator->checkCredentials($credentials, $user);

        $this->assertTrue($result);
    }

    public function testOnAuthenticationSuccess(): void
    {
        $providerKey = 'providerKey';

        $this->request->getSession()
            ->shouldBeCalled()
            ->willReturn($this->session);

        $targetPath = $this->invokeMethod($this->loginFormAuthenticator, 'getTargetPath', [$this->session->reveal(), $providerKey]);

        $this->assertEmpty($targetPath);

        $this->urlGeneratorMock->generate('admin_homepage')
            ->shouldBeCalled()
            ->willReturn('/admin_homepage_url');

        $result = $this->loginFormAuthenticator->onAuthenticationSuccess($this->request->reveal(), $this->tokenInterface->reveal(), $providerKey);

        $this->assertInstanceOf(RedirectResponse::class, $result);
    }

    /**
     * @test
     *
     * @throws ReflectionException
     */
    public function testGetLoginUrlReturnString(): void
    {
        $this->urlGeneratorMock->generate('admin_sign_in')
            ->shouldBeCalled()
            ->willReturn('/admin/login');

        $this->invokeMethod($this->loginFormAuthenticator, 'getLoginUrl');
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on
     * @param string $methodName Method name to call
     * @param array  $parameters array of parameters to pass into method
     *
     * @return mixed method return
     *
     * @throws ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
