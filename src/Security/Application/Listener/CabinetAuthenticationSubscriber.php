<?php

namespace App\Security\Application\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CabinetAuthenticationSubscriber implements EventSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_authentication_failure' => ['onAuthenticationFailureResponse'],
        ];
    }

    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): void
    {
        $exception = $event->getException();

        $message = $this->translator->trans(
            $exception->getMessageKey(),
            $exception->getMessageData(),
            'frontend'
        );
        $data = [
            'username' => [$message],
        ];

        $response = new JsonResponse($data, 400);

        $event->setResponse($response);
    }
}
