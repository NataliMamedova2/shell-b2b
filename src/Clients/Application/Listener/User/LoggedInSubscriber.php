<?php

namespace App\Clients\Application\Listener\User;

use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

final class LoggedInSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(UserRepository $userRepository, ObjectManager $objectManager)
    {
        $this->userRepository = $userRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => ['loggedIn'],
        ];
    }

    public function loggedIn(InteractiveLoginEvent $event): void
    {
        $authenticationToken = $event->getAuthenticationToken();
        /** @var User $user */
        $user = $authenticationToken->getUser();

        if ($user instanceof User) {
            $user->loggedIn(new \DateTimeImmutable());

            $this->userRepository->add($user);
            $this->objectManager->flush();
        }
    }
}
