<?php

namespace App\Import\Domain\Import\File\ValueObject\Status;

final class FailedStatus extends Status
{
    protected $next = [];

    public function getValue(): string
    {
        return Status::FAILED;
    }
}
