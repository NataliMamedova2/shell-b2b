<?php

namespace App\Application\Domain\Exception;

final class InvalidIdentityException extends \Exception
{
    public function __construct($identifier)
    {
        parent::__construct('Invalid identity: '.(string) $identifier);
    }
}
