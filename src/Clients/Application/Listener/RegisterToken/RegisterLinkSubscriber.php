<?php

namespace App\Clients\Application\Listener\RegisterToken;

use App\Clients\Domain\RegisterToken\Register;
use App\Mailer\Template;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Infrastructure\Interfaces\Repository\Repository;
use MailerBundle\Service\Sender;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RegisterLinkSubscriber implements EventSubscriber
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var Sender
     */
    private $sender;

    public function __construct(
        Repository $repository,
        UrlGeneratorInterface $urlGenerator,
        Sender $sender
    ) {
        $this->repository = $repository;
        $this->urlGenerator = $urlGenerator;
        $this->sender = $sender;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Register) {
            return;
        }

        $this->sender->send($entity->getEmail(), Template::REGISTRATION, [
            'token' => $entity->getToken()->getToken(),
        ]);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Register) {
            return;
        }

        $this->sender->send($entity->getEmail(), Template::REGISTRATION, [
            'token' => $entity->getToken()->getToken(),
        ]);
    }
}
