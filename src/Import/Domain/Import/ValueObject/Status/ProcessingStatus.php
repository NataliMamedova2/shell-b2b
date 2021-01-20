<?php

namespace App\Import\Domain\Import\ValueObject\Status;

final class ProcessingStatus extends Status
{
    protected $next = [DoneStatus::class, FailedStatus::class];

    public function getValue(): string
    {
        return Status::IN_PROGRESS;
    }
}
