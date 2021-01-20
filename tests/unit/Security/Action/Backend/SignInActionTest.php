<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security\Action\Backend;

use App\Security\Action\Backend\SignInAction;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ReflectionClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment as Templating;

class SignInActionTest extends TestCase
{
    /**
     * @var MockObject|AuthenticationUtils
     */
    private $authenticationUtils;

    /**
     * @var MockObject|AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var MockObject|UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var MockObject|Templating
     */
    private $templating;

    public function setUp(): void
    {
        $this->authorizationChecker = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->authenticationUtils = $this->prophesize(AuthenticationUtils::class);
        $this->urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $this->templating = $this->prophesize(Templating::class);
    }

    public function testIsFinalClassTrue(): void
    {
        $this->assertTrue((new ReflectionClass(SignInAction::class))->isFinal());
    }

    /**
     * @test
     */
    public function testActionUserIsAuthorizedReturnRedirectResponse(): void
    {
        $action = new SignInAction();

        $this->authorizationChecker
            ->isGranted(Argument::exact('IS_AUTHENTICATED_FULLY'))
            ->shouldBeCalled()
            ->willReturn(true);

        $route = '/admin_homepage';
        $this->urlGenerator
            ->generate(Argument::exact('admin_homepage'))
            ->shouldBeCalled()
            ->willReturn($route);

        $this->authenticationUtils
            ->getLastUsername()
            ->shouldNotBeCalled();

        $this->authenticationUtils
            ->getLastAuthenticationError()
            ->shouldNotBeCalled();

        $this->templating->render(
            Argument::exact('frontend/security/sign_in.html.twig'),
            Argument::exact([
                'lastUsername' => '',
                'error' => '',
            ])
        )
            ->shouldNotBeCalled();

        $result = $action->__invoke(
            $this->authorizationChecker->reveal(),
            $this->authenticationUtils->reveal(),
            $this->urlGenerator->reveal(),
            $this->templating->reveal()
        );

        $response = new RedirectResponse($route);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals($response, $result);
    }

    /**
     * @test
     */
    public function testActionReturnResponse(): void
    {
        $action = new SignInAction();

        $this->authorizationChecker
            ->isGranted(Argument::exact('IS_AUTHENTICATED_FULLY'))
            ->shouldBeCalled()
            ->willReturn(false);

        $route = '/admin_homepage';
        $this->urlGenerator
            ->generate(Argument::exact('admin_homepage'))
            ->shouldNotBeCalled()
            ->willReturn($route);

        $lastUsername = '';
        $this->authenticationUtils
            ->getLastUsername()
            ->shouldBeCalled()
            ->willReturn($lastUsername);

        $errors = null;
        $this->authenticationUtils
            ->getLastAuthenticationError()
            ->shouldBeCalled()
            ->willReturn($errors);

        $render = '<p></p>';
        $this->templating->render(
            Argument::exact('backend/security/sign_in.html.twig'),
            Argument::exact([
                'lastUsername' => $lastUsername,
                'error' => $errors,
            ])
        )
            ->shouldBeCalled()
            ->willReturn($render);

        $result = $action->__invoke(
            $this->authorizationChecker->reveal(),
            $this->authenticationUtils->reveal(),
            $this->urlGenerator->reveal(),
            $this->templating->reveal()
        );

        $response = new Response($render);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals($response->getContent(), $result->getContent());
    }

    /**
     * @test
     */
    public function testLogoutReturnException(): void
    {
        $action = new SignInAction();

        $this->expectException(Exception::class);
        $action->logout();
    }
}
