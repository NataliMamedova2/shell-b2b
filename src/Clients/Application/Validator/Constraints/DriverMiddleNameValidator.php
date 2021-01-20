<?php

namespace App\Clients\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class DriverMiddleNameValidator extends ConstraintValidator
{
    /** @var int */
    const MIN_LENGTH = 2;

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || true === empty($value)) {
            return;
        }

        if (mb_strlen($value) < self::MIN_LENGTH) {
            $this->context->buildViolation('This value should be {{ limit }} or more.')
                ->setParameter('{{ limit }}', self::MIN_LENGTH)
                ->addViolation();

            return;
        }
    }
}
