<?php

namespace App\Media\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MediaValidator extends ConstraintValidator
{
    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Media) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Media');
        }

        if (!isset($value['path']) || empty($value['path'])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Media::IS_REQUIRED_ERROR)
                ->addViolation();
        }
    }
}
