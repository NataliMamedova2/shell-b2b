<?php

namespace App\Clients\Action\Frontend\Document;

use App\Clients\Domain\User\UseCase\Documents\Handler;
use App\Clients\Domain\User\UseCase\Documents\HandlerRequest;
use App\Users\Domain\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment as Templating;
use Symfony\Component\Routing\Annotation\Route;

final class SotaWidgetAction
{
    /**
     * @var Templating
     */
    private $templating;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var Handler */
    private $handler;

    /** @var UserRepository */
    private $userRepository;

    /**
     * SotaWidgetAction constructor.
     * @param Templating $templating
     * @param TokenStorageInterface $tokenStorage
     * @param Handler $handler
     * @param UserRepository $userRepository
     */
    public function __construct(Templating $templating, TokenStorageInterface $tokenStorage, Handler $handler, UserRepository $userRepository)
    {
        $this->templating = $templating;
        $this->tokenStorage = $tokenStorage;
        $this->handler = $handler;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route(
     *     "/admin/users/documents/esp",
     *     name="admin_users_documents_esp",
     *     methods={"GET"}
     * )
     *
     * @throws \Exception
     */
    public function __invoke(): Response
    {
        $token = '';
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            throw new \InvalidArgumentException('User not found');
        }

        $domainUser = $this->userRepository->findByUsernameOrEmail($user->getUsername(), '');
        if (null === $domainUser) {
            throw new \InvalidArgumentException('User not found');
        }
        $token = $domainUser->getSotaToken();
        if (null === $token) {
            $token = \bin2hex(\random_bytes(16));
            $domainUser->setSotaToken($token);

            $handlerRequest = new HandlerRequest();
            $handlerRequest->token = $token;
            $this->handler->handle($handlerRequest, $domainUser);
        }

        return new Response(
            $this->templating->render('backend/clients/user/esp_documents.html.twig', [
                'token' => $token,
                'title' => 'Документи ЕЦП',
            ])
        );
    }
}
