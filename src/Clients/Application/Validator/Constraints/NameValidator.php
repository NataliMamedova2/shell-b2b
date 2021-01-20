<?php

namespace App\Clients\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class NameValidator extends ConstraintValidator
{
    /**
     * @internal
     */
    private const PATTERN = '/^[a-zA-Z-\'` \p{Cyrillic}]+$/u';

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Name) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Name');
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!preg_match(self::PATTERN, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Name::INVALID_NAME_ERROR)
                ->addViolation();

            return;
        }
    }
}
