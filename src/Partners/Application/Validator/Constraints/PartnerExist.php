<?php
namespace App\Partners\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class PartnerExist extends Constraint
{
    public $message = 'Partner not found.';
}