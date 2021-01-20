<?php

declare(strict_types=1);

namespace App\Security\Action\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment as Templating;

final class SignInAction
{
    /**
     * @Route("/admin/sign-in", name="admin_sign_in")
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param AuthenticationUtils           $authenticationUtils
     * @param UrlGeneratorInterface         $urlGenerator
     * @param Templating                    $templating
     *
     * @return Response|RedirectResponse
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(
        AuthorizationCheckerInterface $authorizationChecker,
        AuthenticationUtils $authenticationUtils,
        UrlGeneratorInterface $urlGenerator,
        Templating $templating
    ) {
        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($urlGenerator->generate('admin_homepage'));
        }

        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        return new Response($templating->render('backend/security/sign_in.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error,
        ]));
    }

    /**
     * @Route("/admin/logout", name="admin_logout", methods={"GET"})
     *
     * @throws \Exception
     */
    public function logout(): void
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
