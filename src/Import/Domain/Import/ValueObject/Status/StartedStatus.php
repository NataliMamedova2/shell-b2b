<?php

declare(strict_types=1);

namespace App\Import\Domain\Import\ValueObject\Status;

final class StartedStatus extends Status
{
    protected $next = [ProcessingStatus::class, FailedStatus::class, DoneStatus::class];

    public function getValue(): string
    {
        return Status::STARTED;
    }
}
