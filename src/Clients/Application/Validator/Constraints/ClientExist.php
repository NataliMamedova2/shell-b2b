<?php

namespace App\Clients\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class ClientExist extends Constraint
{
    public $message = 'Client not found.';
}
