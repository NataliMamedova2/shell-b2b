<?php

namespace App\Import\Domain\Import\ValueObject\Status;

final class DoneStatus extends Status
{
    protected $next = [];

    public function getValue(): string
    {
        return Status::DONE;
    }
}
