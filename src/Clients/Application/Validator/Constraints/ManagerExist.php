<?php

namespace App\Clients\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
final class ManagerExist extends Constraint
{
    const NOT_EXIST_ERROR = '12bd9ddf-cb9b-4dcd-a94e-4d22bxf3077e';

    public $message = 'Entity not exist.';

    public $repository;

    public $criteria;

    public $property = 'manager';

    public $errorPath;

    public function getRequiredOptions()
    {
        return ['property', 'repository', 'criteria'];
    }

    /**
     * The validator must be defined as a service with this name.
     *
     * @return string
     */
    public function validatedBy()
    {
        return ManagerExistValidator::class;
    }

    public function getTargets()
    {
        return [self::CLASS_CONSTRAINT];
    }

    public function getDefaultOption()
    {
        return 'property';
    }
}
