<?php

namespace App\Users\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class ManagerIdExist extends Constraint
{

    public const NOT_UNIQUE_ERROR = 'c798db8d-3af0-41f6-8a73-55255375cdca';

    protected static $errorNames = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public $managerId1CMessage = 'This manager1CId {{ manager1CId }} is already used.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return ManagerIdExistValidator::class;
    }
}
