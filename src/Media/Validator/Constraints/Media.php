<?php

namespace App\Media\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
final class Media extends Constraint
{
    const IS_REQUIRED_ERROR = 'f1351bb4-d103-4f24-8988-acbsafc7fd6h';

    public $message = 'This value should not be blank.';
}
