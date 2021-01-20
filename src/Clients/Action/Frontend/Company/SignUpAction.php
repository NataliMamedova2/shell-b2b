<?php

namespace App\Clients\Action\Frontend\Company;

use App\Clients\Domain\RegisterToken\Register;
use App\Clients\Domain\RegisterToken\Repository\RegisterRepository;
use App\Clients\Domain\RegisterToken\UseCase\Delete;
use App\Clients\Domain\User\UseCase\Register\Handler as RegisterUserHandler;
use App\Clients\Domain\User\UseCase\Register\HandlerRequest;
use App\Clients\Domain\User\User;
use App\Clients\View\Form\Company\SignUpFormType;
use App\Users\Infrastructure\Criteria\ManagerForClient;
use App\Users\Infrastructure\Repository\UserRepository;
use Domain\Exception\EntityNotFoundException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment as Templating;
use DateTimeImmutable;
use Exception;

final class SignUpAction
{
    /**
     * @var RegisterRepository
     */
    private $tokenRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var RegisterUserHandler
     */
    private $registerUserHandler;
    /**
     * @var Delete\Handler
     */
    private $deleteTokenHandler;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var Templating
     */
    private $templating;

    public function __construct(
        RegisterRepository $tokenRepository,
        UserRepository $userRepository,
        FormFactoryInterface $formFactory,
        RegisterUserHandler $registerUserHandler,
        Delete\Handler $deleteTokenHandler,
        UrlGeneratorInterface $urlGenerator,
        Templating $templating
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->userRepository = $userRepository;
        $this->formFactory = $formFactory;
        $this->registerUserHandler = $registerUserHandler;
        $this->deleteTokenHandler = $deleteTokenHandler;
        $this->urlGenerator = $urlGenerator;
        $this->templating = $templating;
    }

    /**
     * @Route(
     *     "/{_locale}/company/register/{token}",
     *     name="frontend_company_register",
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
     * @throws Exception
     */
    public function __invoke(Request $request, string $token): Response
    {
        $registerToken = $this->tokenRepository->findByToken($token);

        if (!$registerToken instanceof Register || $registerToken->getToken()->isExpiredTo(new DateTimeImmutable())) {
            throw new NotFoundHttpException('Token not found');
        }

        $client = $registerToken->getClient();
        $manager = $this->userRepository->find([ManagerForClient::class => $client->getId()]);

        if (!$manager instanceof \App\Users\Domain\User\User) {
            throw new EntityNotFoundException(sprintf('Manager (%s) for client (%s) not registered', $client->getManager1CId(), $client->getClient1CId()));
        }

        $registerUserHandlerRequest = new HandlerRequest();
        $registerUserHandlerRequest->email = $registerToken->getEmail();

        $form = $this->formFactory->create(SignUpFormType::class, $registerUserHandlerRequest);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $registerUserHandlerRequest->client = $client;

            $user = $this->registerUserHandler->handle($registerUserHandlerRequest);

            if ($user instanceof User) {
                $deleteHandlerRequest = new Delete\HandlerRequest();
                $deleteHandlerRequest->setId($registerToken->getId());
                $this->deleteTokenHandler->handle($deleteHandlerRequest);
            }

            $redirectLink = $this->urlGenerator->generate('frontend_company_register_successful');

            return new RedirectResponse($redirectLink);
        }

        return new Response(
            $this->templating->render('frontend/company/register.html.twig', [
                'form' => $form->createView(),
                'client' => $registerToken->getClient(),
            ])
        );
    }
}
