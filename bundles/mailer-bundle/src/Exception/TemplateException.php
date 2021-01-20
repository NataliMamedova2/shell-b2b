<?php

namespace MailerBundle\Exception;

final class TemplateException extends \Exception
{
    protected $message = 'Template not found';
}
