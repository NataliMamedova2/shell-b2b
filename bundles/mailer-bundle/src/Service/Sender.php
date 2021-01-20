<?php

namespace MailerBundle\Service;

use MailerBundle\Exception\TemplateException;
use MailerBundle\TemplateBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

final class Sender implements \MailerBundle\Interfaces\Sender
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TemplateBuilder
     */
    private $templateBuilder;

    public function __construct(MailerInterface $mailer, TemplateBuilder $templateBuilder, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->templateBuilder = $templateBuilder;
        $this->logger = $logger;
    }

    /**
     * @param $to
     * @param string $templateKey
     * @param array  $context
     *
     * @throws TemplateException
     * @throws TransportExceptionInterface
     */
    public function send($to, string $templateKey, array $context = []): void
    {
        $this->logger->info(sprintf('Send email to: %s', $to));

        $template = $this->templateBuilder->build($templateKey, $context);

        try {
            $email = (new TemplatedEmail())
                ->to($to)
                ->subject($template->getSubject())
                ->htmlTemplate($template->getTemplatePath())
                ->context($template->getData())
            ;

            $this->mailer->send($email);
            $this->logger->info(sprintf('Send mail to: "%s"', $to));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }
}
