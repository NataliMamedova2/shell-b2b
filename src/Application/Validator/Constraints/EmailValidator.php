<?php

namespace App\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class EmailValidator extends \Symfony\Component\Validator\Constraints\EmailValidator
{
    private $emailPattern = '/^[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9]{2,63}(?:[a-zA-Z0-9-][a-zA-Z0-9])?)+$/';

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Email || empty($value)) {
            return;
        }

        parent::validate($value, $constraint);

        if (!preg_match($this->emailPattern, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setCode(Email::INVALID_DOMAIN_EXTENSION)
                ->setTranslationDomain('frontend')
                ->addViolation();
        }
    }
}
