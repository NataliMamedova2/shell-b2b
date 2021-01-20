<?php

namespace App\Clients\Action\Frontend\User;

use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\UseCase\RecoverPass\Handler;
use App\Clients\Domain\User\UseCase\RecoverPass\HandlerRequest;
use App\Clients\Domain\User\User;
use App\Clients\View\RecoverPass\RecoverPassFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment as Templating;

final class PassRecoverAction
{
    /**
     * @var Templating
     */
    private $templating;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        UserRepository $userRepository,
        Templating $templating,
        FormFactoryInterface $formFactory,
        Handler $handler,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->userRepository = $userRepository;
        $this->templating = $templating;
        $this->formFactory = $formFactory;
        $this->handler = $handler;
        $this->serializer = $serializer;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route(
     *     "/{_locale}/pass/recover/{token}",
     *     name="frontend_pass_recover",
     *     methods={"GET", "POST"},
     *     defaults={"_locale": "%locale%"},
     *     requirements={
     *          "_locale": "%routing.locales%",
     *          "token": ".+"
     *     }
     * )
     *
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function __invoke(Request $request, string $token): Response
    {
        $user = $this->userRepository->findByToken($token);

        if (!$user instanceof User || $user->getRestoreToken()->isExpiredTo(new \DateTimeImmutable())) {
            throw new NotFoundHttpException('Token not found');
        }

        $passRecoverRequest = new HandlerRequest();

        $form = $this->formFactory->create(RecoverPassFormType::class, $passRecoverRequest);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passRecoverRequest->token = $token;
            $passRecoverRequest->password = $form->get('password')->getNormData();
            $passRecoverRequest->repeatPassword = $form->get('repeatPassword')->getNormData();

            $this->handler->handle($passRecoverRequest);
            $redirectLink = $this->urlGenerator->generate('frontend_pass_recover_successful');

            return new RedirectResponse($redirectLink);
        }

        return new Response(
            $this->templating->render('frontend/pass_recovery/pass_recover.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }
}
