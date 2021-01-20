<?php

namespace App\Clients\Application\Listener\CardOrder;

use App\Clients\Domain\CardOrder\Order;
use App\Users\Domain\User\Repository\UserRepository;
use App\Mailer\Template;
use App\Users\Domain\User\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use MailerBundle\Interfaces\Sender;

final class OrderNewCardSubscriber implements EventSubscriber
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var Sender
     */
    private $sender;

    public function __construct(
        UserRepository $repository,
        Sender $sender
    ) {
        $this->repository = $repository;
        $this->sender = $sender;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Order) {
            return;
        }

        $user = $entity->getUser();
        $company = $user->getCompany();

        $manager1CId = $company->getClient()->getManager1CId();

        $manager = $this->repository->find([
            'manager1CId_equalTo' => $manager1CId,
        ]);
        if ($manager instanceof User) {
            $this->sender->send($manager->getEmail(), Template::ORDER_NEW_CARD, [
                'client' => $company->getClient(),
                'name' => $entity->getName(),
                'count' => $entity->getCount(),
                'phone' => $entity->getPhone(),
            ]);
        }
    }
}
