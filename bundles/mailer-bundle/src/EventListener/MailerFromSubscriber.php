<?php

namespace MailerBundle\EventListener;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

final class MailerFromSubscriber implements EventSubscriberInterface
{
    private $fromEmail;
    private $fromName;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $mailerConfig = $parameterBag->get('mailer');

        $this->fromEmail = $mailerConfig['from_email'] ?? '';
        $this->fromName = $mailerConfig['from_name'] ?? '';
    }

    public static function getSubscribedEvents()
    {
        return [MessageEvent::class => 'onMessageSend'];
    }

    public function onMessageSend(MessageEvent $event)
    {
        $message = $event->getMessage();

        // make sure it's an Email object
        if (!$message instanceof Email && (!empty($this->fromEmail) && !empty($this->fromName))) {
            return;
        }

        $message->from(new Address($this->fromEmail, $this->fromName));
    }
}
