<?php
namespace App\Clients\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class DriverMiddleName extends Constraint
{
    public $message = 'This value should be {{ limit }} or more.';
}