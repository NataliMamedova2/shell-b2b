<?php

namespace App\Clients\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class UserNotExist extends Constraint
{

    public const NOT_EXIST_ERROR = '02c43acc-5b4c-47ef-9ace-2c617c9e8bb9';

    protected static $errorNames = [
        self::NOT_EXIST_ERROR => 'NOT_EXIST_ERROR',
    ];

    public $messageNotExist = 'Such user not exist.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return UserNotExistValidator::class;
    }
}
