<?php

namespace App\Application\Domain\Exception;

final class InvalidArgumentException extends \InvalidArgumentException
{
    public function __construct($value, array $allowed_types)
    {
        parent::__construct(sprintf('Argument "%s" is invalid. Allowed types for argument are "%s".', $value, implode(', ', $allowed_types)));
    }
}
