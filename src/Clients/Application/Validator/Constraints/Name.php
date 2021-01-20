<?php

namespace App\Clients\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class Name extends Constraint
{
    const INVALID_NAME_ERROR = 'bd79c0ab-ddba-46cc-a703-a7a4b08de310';

    public $message = 'This value is not a valid name.';
}
