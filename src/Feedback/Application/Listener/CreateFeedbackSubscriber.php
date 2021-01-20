<?php

namespace App\Feedback\Application\Listener;

use App\Feedback\Domain\Feedback\Feedback;
use App\Mailer\Template;
use App\Users\Domain\User\Repository\UserRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use MailerBundle\Interfaces\Sender;

class CreateFeedbackSubscriber implements EventSubscriber
{
    /**
     * @var Sender
     */
    private $sender;
    /**
     * @var UserRepository
     */
    private $userRepository; 

    /** @var string */
    private const COMPLAIN_CATEGORY = 'complaints';

    public function __construct(
        Sender $sender,
        UserRepository $userRepository
    ) {
        $this->sender = $sender;
        $this->userRepository = $userRepository;
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

        if (!$entity instanceof Feedback) {
            return;
        }

        $category = $entity->getCategory();

        $this->sender->send($category->getManagerEmail(), Template::FEEDBACK, [
            'feedback' => $entity,
        ]);

        $this->sender->send('Denys.Vynohradskyi@shell.com', Template::FEEDBACK, [
            'feedback' => $entity,
        ]);

        if (self::COMPLAIN_CATEGORY === $category) {
            $this->sender->send('viktor.frolov@shell.com ', Template::FEEDBACK, [
                'feedback' => $entity,
            ]);
        }
    }
}
