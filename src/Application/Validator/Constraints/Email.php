<?php

namespace App\Application\Validator\Constraints;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Email extends \Symfony\Component\Validator\Constraints\Email
{
    public const INVALID_DOMAIN_EXTENSION = '7da53a8b-56f3-4288-bb3e-ee9ede4ef9a2';
}
