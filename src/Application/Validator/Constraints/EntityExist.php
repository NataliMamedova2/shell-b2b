<?php

namespace App\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
final class EntityExist extends Constraint
{
    const NOT_EXIST_ERROR = '12bd9ddf-cb9b-4dcd-a94e-4d22bxf3077e';

    public $message = 'Entity not exist.';

    /**
     * @var string
     */
    public $repository;

    public $criteriaName = 'id_equalTo';

    public function getRequiredOptions()
    {
        return ['repository'];
    }

    public function getTargets()
    {
        return [self::PROPERTY_CONSTRAINT];
    }
}
