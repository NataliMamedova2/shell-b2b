<?php

namespace MailerBundle\Interfaces;

interface Sender
{
    public function send($to, string $templateKey, array $context = []): void;
}